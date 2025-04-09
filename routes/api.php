<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyPaymentController;
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

Route::post('/user/login', [AuthController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('flight')->group(function(){
    Route::post('/search', [FlightController::class, 'search']);
    Route::post('/pricing/{searchId}/flight/{id}', [FlightController::class, 'pricing']);
    Route::post('/booking/{searchId}/{flightId}', [FlightController::class, 'createOrder']);
    Route::post('/payment/{bookingId}', [FlightController::class, 'makePayment']);
});

Route::prefix('callback')->group(function(){
    Route::post('/sslcz/success', [VerifyPaymentController::class, 'sslSuccess']);
    Route::post('/sslcz/failed', [VerifyPaymentController::class, 'sslFail']);
    Route::post('/sslcz/cancelled', [VerifyPaymentController::class, 'sslFail']);
    Route::post('/sslcz/ipn', [VerifyPaymentController::class, 'sslSuccess'])->middleware('sslc.ipn.verify');
});