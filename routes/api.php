<?php

use App\Http\Controllers\Api\AgentComputerInventoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Agent inventory
|--------------------------------------------------------------------------
|
| Reporting agents push a machine's inventory here over HTTP, authenticating
| with a bearer token. The limits behind 'agent-inventory' are defined in
| AppServiceProvider.
|
*/
Route::post('/agent/computer-inventory', [AgentComputerInventoryController::class, 'store'])
    ->middleware('throttle:agent-inventory')
    ->name('api.agent.computer-inventory');
