<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::post('/servers/{id}/metrics', [ApiController::class, 'updateMetrics']);
Route::post('/servers/{id}/status', [ApiController::class, 'updateStatus']);
Route::post('/deployments/{id}/finish', [ApiController::class, 'finishDeployment']);
