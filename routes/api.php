<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgentComputerInventoryController;

Route::post('/agent/computer-inventory', [AgentComputerInventoryController::class, 'store']);
