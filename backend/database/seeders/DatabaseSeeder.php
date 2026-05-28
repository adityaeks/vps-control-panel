<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Server;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@vps.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password')
            ]
        );

        echo "Seeded Administrator User:\n";
        echo "Email: admin@vps.com\n";
        echo "Password: password\n\n";

        // Create a default local VPS server to populate the dashboard immediately
        $server = Server::updateOrCreate(
            ['ip_address' => '127.0.0.1'],
            [
                'name' => 'Local VPS Node',
                'agent_port' => 3000,
                'auth_token' => 'secure-vps-token-12345',
                'status' => 'offline'
            ]
        );

        echo "Seeded default server 'Local VPS Node'\n";

        // Create a default dummy project
        Project::updateOrCreate(
            ['name' => 'Antigravity Micro-Service'],
            [
                'server_id' => $server->id,
                'repository_url' => 'https://github.com/google-deepmind/antigravity.git',
                'branch' => 'main',
                'deploy_script' => "echo \"[1/3] Cloning repository...\"\nsleep 1\necho \"[2/3] Resolving dependencies...\"\nsleep 1.5\necho \"[3/3] Initiating containers...\"\nsleep 1\necho \"SUCCESS: Service online!\"",
                'status' => 'idle'
            ]
        );

        echo "Seeded default Project 'Antigravity Micro-Service'\n";
    }
}
