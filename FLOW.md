# FLOW.md

# SYSTEM FLOW

---

# 1. USER LOGIN FLOW

User
↓
Login Page
↓
Laravel Authentication
↓
Dashboard

---

# 2. VPS CONNECTION FLOW

Dashboard
↓
Generate API Token
↓
Install Agent on VPS
↓
Agent Connects to WebSocket
↓
Server Registered

---

# 3. HEARTBEAT FLOW

Agent
↓
Collect Metrics

* CPU
* RAM
* Disk
* Uptime
  ↓
  Send Heartbeat
  ↓
  Dashboard Updates Status

---

# 4. DEPLOYMENT FLOW

User Click Deploy
↓
Dashboard Creates Job
↓
Send Job to VPS Agent
↓
Agent Executes Commands

Example:

* git pull
* composer install
* npm run build
* php artisan migrate

↓
Realtime Logs Sent Back
↓
Deployment Finished
↓
Save Deployment History

---

# 5. TERMINAL FLOW

Browser
↓
xterm.js
↓
WebSocket
↓
Dashboard
↓
Agent
↓
PTY Bash Shell
↓
Linux Server

---

# 6. REALTIME LOG FLOW

Application Logs
↓
Agent Reads Logs
↓
WebSocket Stream
↓
Dashboard Viewer

---

# 7. PROJECT MANAGEMENT FLOW

Create Project
↓
Select VPS
↓
Set Repository
↓
Set Deployment Script
↓
Save Project

---

# 8. SECURITY FLOW

User Login
↓
JWT Authentication
↓
Permission Validation
↓
Command Validation
↓
Execute Command

---

# 9. MULTI VPS FLOW

Dashboard
├── VPS Production
├── VPS Staging
├── VPS Development
└── VPS Bot

Each VPS:

* own agent
* own projects
* own metrics
* own terminal session

---

# 10. AGENT FLOW

Agent Starts
↓
Read Config
↓
Connect WebSocket
↓
Authenticate
↓
Wait For Command
↓
Execute Command
↓
Send Result

---

# 11. DATABASE FLOW

Users
↓
Servers
↓
Projects
↓
Deployments
↓
Logs

---

# 12. FUTURE SCALING FLOW

Load Balancer
↓
Multiple Dashboard Nodes
↓
Redis Pub/Sub
↓
Multiple VPS Agents

---

# 13. ERROR HANDLING FLOW

Command Failed
↓
Capture STDERR
↓
Send Error Logs
↓
Save Failure Status
↓
Show Notification

---

# 14. DOCKER FLOW

Dashboard
↓
Agent
↓
Docker CLI
↓
Container Control

Examples:

* docker ps
* docker restart
* docker logs

---

# 15. WEBSOCKET FLOW

Frontend
↕
Realtime WebSocket Server
↕
VPS Agent

Handles:

* terminal
* logs
* metrics
* deployment status
