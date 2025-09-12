<?php

use App\Http\Controllers\ReservationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('reservations', ReservationsController::class)->only(['index', 'store']);
