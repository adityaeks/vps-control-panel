<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Server CRUD
    Route::get('/server/create', [DashboardController::class, 'createServer'])->name('server.create');
    Route::post('/server/create', [DashboardController::class, 'storeServer']);
    Route::get('/server/{id}', [DashboardController::class, 'showServer'])->name('server.show');
    Route::get('/server/{id}/edit', [DashboardController::class, 'editServer'])->name('server.edit');
    Route::post('/server/{id}/edit', [DashboardController::class, 'updateServer']);
    Route::delete('/server/{id}', [DashboardController::class, 'deleteServer'])->name('server.delete');
    
    // Server Realtime Views
    Route::get('/server/{id}/terminal', [DashboardController::class, 'showTerminal'])->name('server.terminal');
    Route::get('/server/{id}/docker', [DashboardController::class, 'showDocker'])->name('server.docker');

    // Project CRUD & Deployments
    Route::get('/server/{serverId}/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/server/{serverId}/project/create', [ProjectController::class, 'store']);
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/project/{id}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::post('/project/{id}/edit', [ProjectController::class, 'update']);
    Route::delete('/project/{id}', [ProjectController::class, 'delete'])->name('project.delete');
    
    Route::post('/project/{id}/deploy', [ProjectController::class, 'deploy'])->name('project.deploy');
    Route::get('/deployment/{id}', [ProjectController::class, 'showDeployment'])->name('deployment.show');
});
