<?php

use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UnsubscriptionController;
use App\Http\Controllers\WeatherAPIController;
use Illuminate\Support\Facades\Route;
/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/api/weather', WeatherAPIController::class);
Route::post('/api/subscribe', SubscriptionController::class);

Route::get('/subscribe', function () {
    return view('subscribe');
});

Route::get('/api/confirm/{token}', ConfirmationController::class)->name('confirm.route');

Route::get('/subscription', function () {
    return view('subscription');
})->name('subscription.route');

Route::get('/api/unsubscribe/{token}', UnsubscriptionController::class);
