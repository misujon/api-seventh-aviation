<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->prefix('flight')->group(function(){
    Route::post('/search', [FlightController::class, 'search']);
    Route::post('/pricing/{searchId}/flight/{id}', [FlightController::class, 'pricing']);
    Route::post('/booking/{searchId}/{flightId}', [FlightController::class, 'createOrder']);
});
