<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Project;
use App\Models\Deployment;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
    public function create($serverId)
    {
        $server = Server::findOrFail($serverId);
        return view('project.create', compact('server'));
    }

    public function store(Request $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $request->validate([
            'name' => 'required|string|max:255',
            'repository_url' => 'required|string',
            'branch' => 'required|string',
            'install_script' => 'nullable|string',
            'deploy_script' => 'nullable|string'
        ]);

        $deployScript = $request->input('deploy_script') ?? "git pull origin " . $request->input('branch') . "\nnpm run build";
        $deployScript = str_replace("\r\n", "\n", $deployScript);

        $installScript = $request->input('install_script');
        if ($installScript) {
            $installScript = str_replace("\r\n", "\n", $installScript);
        }

        $project = Project::create([
            'server_id' => $server->id,
            'name' => $request->input('name'),
            'repository_url' => $request->input('repository_url'),
            'branch' => $request->input('branch'),
            'install_script' => $installScript,
            'deploy_script' => $deployScript,
            'status' => 'idle'
        ]);

        return redirect()->route('server.show', $server->id)->with('success', 'Project created successfully.');
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        $deployments = $project->deployments()->orderBy('created_at', 'desc')->get();
        return view('project.show', compact('project', 'deployments'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('project.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'repository_url' => 'required|string',
            'branch' => 'required|string',
            'install_script' => 'nullable|string',
            'deploy_script' => 'nullable|string'
        ]);

        $data = $request->only('name', 'repository_url', 'branch');
        
        $deployScript = $request->input('deploy_script');
        if ($deployScript) {
            $data['deploy_script'] = str_replace("\r\n", "\n", $deployScript);
        }
        
        $installScript = $request->input('install_script');
        if ($installScript) {
            $data['install_script'] = str_replace("\r\n", "\n", $installScript);
        }

        $project->update($data);

        return redirect()->route('project.show', $project->id)->with('success', 'Project updated successfully.');
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);
        $serverId = $project->server_id;
        $project->delete();

        return redirect()->route('server.show', $serverId)->with('success', 'Project deleted successfully.');
    }

    public function deploy($id)
    {
        $project = Project::findOrFail($id);
        $server = $project->server;

        if ($server->status !== 'online') {
            return back()->with('error', 'Cannot deploy. The target VPS agent is offline.');
        }

        // Create deployment record
        $deployment = Deployment::create([
            'project_id' => $project->id,
            'status' => 'running',
            'started_at' => now()
        ]);

        $project->update(['status' => 'deploying']);

        // Send deploy command to WebSocket Gateway API
        try {
            $response = Http::withToken(env('GATEWAY_TOKEN', 'secure-vps-token-12345'))
                ->post(env('GATEWAY_URL', 'http://127.0.0.1:3000') . '/api/command', [
                    'server_id' => $server->id,
                    'action' => 'deploy',
                    'data' => [
                        'deployment_id' => $deployment->id,
                        'command' => $project->deploy_script
                    ]
                ]);

            if ($response->successful()) {
                return redirect()->route('deployment.show', $deployment->id)->with('success', 'Deployment started successfully!');
            } else {
                $deployment->update(['status' => 'failed', 'log_output' => 'Gateway error: ' . $response->body()]);
                $project->update(['status' => 'failed']);
                return back()->with('error', 'Failed to communicate with WebSocket Gateway.');
            }
        } catch (\Exception $e) {
            $deployment->update(['status' => 'failed', 'log_output' => 'Connection to Gateway failed: ' . $e->getMessage()]);
            $project->update(['status' => 'failed']);
            return back()->with('error', 'Cannot connect to WebSocket Gateway server.');
        }
    }

    public function showDeployment($id)
    {
        $deployment = Deployment::findOrFail($id);
        $project = $deployment->project;
        return view('project.deployment', compact('deployment', 'project'));
    }
}
