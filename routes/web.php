<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SslCommerzPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('auth.signin');
})->name('login')->middleware('guest');

Route::get('/forgot-password', function () {
    return view('auth.forget');
})->name('forget.password')->middleware('guest');

Route::get('/reset-password', function () {
    return view('auth.reset', ['request' => request()]);
})->name('forget.password')->middleware('guest');

Route::middleware('auth:admin')->group(function(){
    Route::any('/logout', function () {
        Auth::guard('admin')->logout();
        return redirect()->route('login');
    })->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::resource('/bookings', BookingController::class);
});

// SSLCOMMERZ Start
// Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
// Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

// Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
// Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
// Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
// Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

// Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END
