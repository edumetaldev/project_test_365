<?php

use App\Http\Controllers\PassengerController;
use App\Http\Controllers\ReservationChangeStatusController;
use App\Http\Controllers\ReservationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Reservations API Routes
Route::get('/reservations', [ReservationsController::class, 'index']);
Route::post('/reservations', [ReservationsController::class, 'store']);
Route::post('/reservations/{reservationId}/status', ReservationChangeStatusController::class);
Route::resource('/passengers', PassengerController::class);
