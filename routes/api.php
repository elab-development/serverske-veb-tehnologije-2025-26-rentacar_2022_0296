<?php

use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\RentalController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::apiResource('locations', LocationController::class);
Route::apiResource('cars', CarController::class);
Route::apiResource('rentals', RentalController::class);
