<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'agent_port',
        'auth_token',
        'status',
        'cpu_usage',
        'ram_usage',
        'disk_usage',
        'uptime',
        'last_heartbeat'
    ];

    protected $casts = [
        'last_heartbeat' => 'datetime',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
