<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'repository_url',
        'branch',
        'install_script',
        'deploy_script',
        'status',
        'env_variables'
    ];

    protected $casts = [
        'env_variables' => 'array'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }
}
