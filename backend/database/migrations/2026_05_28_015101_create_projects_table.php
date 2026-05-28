<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('repository_url');
            $table->string('branch')->default('main');
            $table->text('install_script')->nullable();
            $table->text('deploy_script')->nullable();
            $table->string('status')->default('idle'); // idle, deploying, success, failed
            $table->text('env_variables')->nullable(); // stored as encrypted or plain json
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
