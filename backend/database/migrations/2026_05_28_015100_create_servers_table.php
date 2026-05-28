<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->integer('agent_port')->default(3000);
            $table->string('auth_token')->default('secure-vps-token-12345');
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->integer('cpu_usage')->nullable();
            $table->integer('ram_usage')->nullable();
            $table->integer('disk_usage')->nullable();
            $table->integer('uptime')->nullable();
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
