const { WebSocketServer } = require('ws');
const http = require('http');
const url = require('url');

const PORT = process.env.PORT || 3000;
const AUTH_TOKEN = process.env.AUTH_TOKEN || 'secure-vps-token-12345';

// Store connections
// Key: server_id, Value: WebSocket connection
const agents = new Map();
// Key: server_id, Value: Set of WebSocket client connections for metrics
const metricClients = new Map();
// Key: server_id, Value: Set of WebSocket client connections for terminal
const terminalClients = new Map();
// Key: deployment_id, Value: Set of WebSocket client connections for deployment logs
const deploymentClients = new Map();

// Create HTTP server
const server = http.createServer((req, res) => {
  const parsedUrl = url.parse(req.url, true);
  
  // CORS Headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

  if (req.method === 'OPTIONS') {
    res.writeHead(200);
    res.end();
    return;
  }

  // API endpoint for Laravel to trigger commands
  if (req.method === 'POST' && parsedUrl.pathname === '/api/command') {
    let body = '';
    req.on('data', chunk => { body += chunk; });
    req.on('end', () => {
      try {
        const payload = JSON.parse(body);
        const { server_id, action, data } = payload;

        // Verify token
        const authHeader = req.headers['authorization'];
        if (!authHeader || authHeader !== `Bearer ${AUTH_TOKEN}`) {
          res.writeHead(401, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ error: 'Unauthorized' }));
          return;
        }

        const agent = agents.get(String(server_id));
        if (!agent) {
          res.writeHead(404, { 'Content-Type': 'application/json' });
          res.end(JSON.stringify({ error: 'Agent offline' }));
          return;
        }

        // Send to agent
        agent.send(JSON.stringify({
          type: action,
          ...data
        }));

        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ success: true }));
      } catch (err) {
        res.writeHead(400, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ error: 'Invalid JSON body' }));
      }
    });
    return;
  }

  // Health check
  if (parsedUrl.pathname === '/health') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({
      status: 'ok',
      agents: Array.from(agents.keys()).map(id => ({ server_id: id }))
    }));
    return;
  }

  res.writeHead(404);
  res.end('Not Found');
});

// Setup WebSocket Server
const wss = new WebSocketServer({ noServer: true });

server.on('upgrade', (request, socket, head) => {
  const parsedUrl = url.parse(request.url, true);
  const { pathname, query } = parsedUrl;

  // Authenticate token
  if (query.token !== AUTH_TOKEN) {
    socket.write('HTTP/1.1 401 Unauthorized\r\n\r\n');
    socket.destroy();
    return;
  }

  wss.handleUpgrade(request, socket, head, (ws) => {
    wss.emit('connection', ws, request);
  });
});

wss.on('connection', (ws, request) => {
  const parsedUrl = url.parse(request.url, true);
  const { query } = parsedUrl;
  const role = query.role; // 'agent' or 'client'
  const serverId = String(query.server_id);

  if (role === 'agent') {
    console.log(`Agent connected for server: ${serverId}`);
    agents.set(serverId, ws);

    // Notify Laravel backend that server is online
    notifyLaravelServerStatus(serverId, 'online');

    ws.on('message', (message) => {
      try {
        const payload = JSON.parse(message);
        
        if (payload.type === 'metrics') {
          // Forward metrics to all listening clients
          const clients = metricClients.get(serverId);
          if (clients) {
            clients.forEach(client => {
              if (client.readyState === ws.OPEN) {
                client.send(JSON.stringify({ type: 'metrics', data: payload.data }));
              }
            });
          }
          // Also save in Laravel database via HTTP call
          saveMetricsToLaravel(serverId, payload.data);
        } 
        
        else if (payload.type === 'terminal_output') {
          // Forward terminal output to listening clients
          const clients = terminalClients.get(serverId);
          if (clients) {
            clients.forEach(client => {
              if (client.readyState === ws.OPEN) {
                client.send(JSON.stringify({ type: 'terminal_output', data: payload.data }));
              }
            });
          }
        } 
        
        else if (payload.type === 'deploy_log') {
          // Forward deploy logs to listening clients
          const deployId = String(payload.deployment_id);
          const clients = deploymentClients.get(deployId);
          if (clients) {
            clients.forEach(client => {
              if (client.readyState === ws.OPEN) {
                client.send(JSON.stringify({ type: 'deploy_log', data: payload.data }));
              }
            });
          }
        }

        else if (payload.type === 'deploy_finished') {
          // Notify Laravel backend that deploy finished
          const deployId = String(payload.deployment_id);
          notifyLaravelDeployFinished(deployId, payload.status, payload.log);
        }

        else if (payload.type === 'docker_list_res') {
          // Docker list containers response -> send back to clients requesting docker info
          const clients = terminalClients.get(serverId); // or reuse terminal channel for general comms
          if (clients) {
            clients.forEach(client => {
              if (client.readyState === ws.OPEN) {
                client.send(JSON.stringify({ type: 'docker_list_res', data: payload.data }));
              }
            });
          }
        }

        else if (payload.type === 'docker_logs_res') {
          const clients = terminalClients.get(serverId);
          if (clients) {
            clients.forEach(client => {
              if (client.readyState === ws.OPEN) {
                client.send(JSON.stringify({ type: 'docker_logs_res', data: payload.data }));
              }
            });
          }
        }

      } catch (err) {
        console.error('Error parsing agent message:', err);
      }
    });

    ws.on('close', () => {
      console.log(`Agent disconnected for server: ${serverId}`);
      if (agents.get(serverId) === ws) {
        agents.delete(serverId);
        notifyLaravelServerStatus(serverId, 'offline');
      }
    });

  } else if (role === 'client') {
    const type = query.type; // 'metrics', 'terminal', or 'deploy'
    const deployId = query.deployment_id ? String(query.deployment_id) : null;

    console.log(`Client connected: Server=${serverId}, Type=${type}, Deploy=${deployId}`);

    if (type === 'metrics') {
      if (!metricClients.has(serverId)) {
        metricClients.set(serverId, new Set());
      }
      metricClients.get(serverId).add(ws);
    } 
    
    else if (type === 'terminal') {
      if (!terminalClients.has(serverId)) {
        terminalClients.set(serverId, new Set());
      }
      terminalClients.get(serverId).add(ws);

      // Notify agent to start terminal
      const agent = agents.get(serverId);
      if (agent && agent.readyState === ws.OPEN) {
        agent.send(JSON.stringify({ type: 'terminal_start' }));
      }
    } 
    
    else if (type === 'deploy' && deployId) {
      if (!deploymentClients.has(deployId)) {
        deploymentClients.set(deployId, new Set());
      }
      deploymentClients.get(deployId).add(ws);
    }

    ws.on('message', (message) => {
      // Forward client input to agent
      const agent = agents.get(serverId);
      if (!agent || agent.readyState !== ws.OPEN) {
        ws.send(JSON.stringify({ type: 'error', message: 'VPS agent is currently offline.' }));
        return;
      }

      try {
        const payload = JSON.parse(message);
        if (payload.type === 'terminal_input') {
          agent.send(JSON.stringify({ type: 'terminal_input', data: payload.data }));
        } else if (payload.type === 'terminal_resize') {
          agent.send(JSON.stringify({ type: 'terminal_resize', cols: payload.cols, rows: payload.rows }));
        } else if (payload.type === 'docker_list') {
          agent.send(JSON.stringify({ type: 'docker_list' }));
        } else if (payload.type === 'docker_restart') {
          agent.send(JSON.stringify({ type: 'docker_restart', container_id: payload.container_id }));
        } else if (payload.type === 'docker_logs') {
          agent.send(JSON.stringify({ type: 'docker_logs', container_id: payload.container_id }));
        }
      } catch (err) {
        // Raw keyboard data (fallback for simpler terminal inputs)
        agent.send(JSON.stringify({ type: 'terminal_input', data: message.toString() }));
      }
    });

    ws.on('close', () => {
      if (type === 'metrics') {
        metricClients.get(serverId)?.delete(ws);
      } else if (type === 'terminal') {
        terminalClients.get(serverId)?.delete(ws);
        // If no more terminal clients, notify agent to stop PTY
        if (!terminalClients.get(serverId) || terminalClients.get(serverId).size === 0) {
          const agent = agents.get(serverId);
          if (agent && agent.readyState === ws.OPEN) {
            agent.send(JSON.stringify({ type: 'terminal_stop' }));
          }
        }
      } else if (type === 'deploy' && deployId) {
        deploymentClients.get(deployId)?.delete(ws);
      }
    });
  }
});

// Helper functions to sync with Laravel API
function saveMetricsToLaravel(serverId, metrics) {
  const data = JSON.stringify(metrics);
  const req = http.request({
    hostname: '127.0.0.1',
    port: 8000,
    path: `/api/servers/${serverId}/metrics`,
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Content-Length': Buffer.byteLength(data),
      'Authorization': `Bearer ${AUTH_TOKEN}`
    }
  }, (res) => {
    res.resume(); // consume response
  });
  req.on('error', () => {}); // Ignore offline errors
  req.write(data);
  req.end();
}

function notifyLaravelServerStatus(serverId, status) {
  const data = JSON.stringify({ status });
  const req = http.request({
    hostname: '127.0.0.1',
    port: 8000,
    path: `/api/servers/${serverId}/status`,
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Content-Length': Buffer.byteLength(data),
      'Authorization': `Bearer ${AUTH_TOKEN}`
    }
  }, (res) => {
    res.resume();
  });
  req.on('error', () => {});
  req.write(data);
  req.end();
}

function notifyLaravelDeployFinished(deployId, status, log) {
  const data = JSON.stringify({ status, log });
  const req = http.request({
    hostname: '127.0.0.1',
    port: 8000,
    path: `/api/deployments/${deployId}/finish`,
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Content-Length': Buffer.byteLength(data),
      'Authorization': `Bearer ${AUTH_TOKEN}`
    }
  }, (res) => {
    res.resume();
  });
  req.on('error', () => {});
  req.write(data);
  req.end();
}

server.listen(PORT, '0.0.0.0', () => {
  console.log(`WebSocket Gateway running on port ${PORT}`);
});
