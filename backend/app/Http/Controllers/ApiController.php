<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Deployment;
use App\Models\Project;

class ApiController extends Controller
{
    private $secureToken = 'secure-vps-token-12345';

    private function validateToken(Request $request)
    {
        $token = $request->bearerToken();
        return $token === $this->secureToken;
    }

    public function updateMetrics(Request $request, $id)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $server->update([
            'status' => 'online',
            'cpu_usage' => $request->input('cpu'),
            'ram_usage' => $request->input('ram'),
            'disk_usage' => $request->input('disk'),
            'uptime' => $request->input('uptime'),
            'last_heartbeat' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $server = Server::findOrFail($id);
        $server->update([
            'status' => $request->input('status')
        ]);

        return response()->json(['success' => true]);
    }

    public function finishDeployment(Request $request, $id)
    {
        if (!$this->validateToken($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $deployment = Deployment::findOrFail($id);
        $deployment->update([
            'status' => $request->input('status'),
            'log_output' => $request->input('log'),
            'finished_at' => now()
        ]);

        // Update parent project status
        $project = $deployment->project;
        $project->update([
            'status' => $request->input('status') === 'success' ? 'success' : 'failed'
        ]);

        return response()->json(['success' => true]);
    }
}
