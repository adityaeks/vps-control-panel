<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Project;
use App\Models\Deployment;

class DashboardController extends Controller
{
    public function index()
    {
        $servers = Server::all();
        $totalServers = $servers->count();
        $onlineServers = $servers->where('status', 'online')->count();
        $totalProjects = Project::count();

        // Calculate average metrics for online servers
        $avgCpu = $servers->where('status', 'online')->avg('cpu_usage') ?? 0;
        $avgRam = $servers->where('status', 'online')->avg('ram_usage') ?? 0;
        $avgDisk = $servers->where('status', 'online')->avg('disk_usage') ?? 0;

        return view('dashboard.index', compact(
            'servers', 
            'totalServers', 
            'onlineServers', 
            'totalProjects',
            'avgCpu',
            'avgRam',
            'avgDisk'
        ));
    }

    public function createServer()
    {
        return view('dashboard.server-create');
    }

    public function storeServer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string',
            'agent_port' => 'required|integer',
            'auth_token' => 'required|string'
        ]);

        Server::create([
            'name' => $request->input('name'),
            'ip_address' => $request->input('ip_address'),
            'agent_port' => $request->input('agent_port'),
            'auth_token' => $request->input('auth_token'),
            'status' => 'offline'
        ]);

        return redirect()->route('dashboard')->with('success', 'Server added successfully. Install agent to connect!');
    }

    public function showServer($id)
    {
        $server = Server::findOrFail($id);
        $projects = $server->projects;
        
        // Scan local directories if on the same VPS node
        $localDirectories = [];
        $path = '/var/www/html';
        if (is_dir($path) && is_readable($path)) {
            $dirs = glob($path . '/*', GLOB_ONLYDIR);
            $localDirectories = array_map('basename', $dirs);
        }

        return view('dashboard.server-show', compact('server', 'projects', 'localDirectories'));
    }

    public function editServer($id)
    {
        $server = Server::findOrFail($id);
        return view('dashboard.server-edit', compact('server'));
    }

    public function updateServer(Request $request, $id)
    {
        $server = Server::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string',
            'agent_port' => 'required|integer',
            'auth_token' => 'required|string'
        ]);

        $server->update($request->only('name', 'ip_address', 'agent_port', 'auth_token'));

        return redirect()->route('server.show', $id)->with('success', 'Server updated successfully.');
    }

    public function deleteServer($id)
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return redirect()->route('dashboard')->with('success', 'Server deleted successfully.');
    }

    public function showTerminal($id)
    {
        $server = Server::findOrFail($id);
        return view('dashboard.server-terminal', compact('server'));
    }

    public function showDocker($id)
    {
        $server = Server::findOrFail($id);
        return view('dashboard.server-docker', compact('server'));
    }
}
