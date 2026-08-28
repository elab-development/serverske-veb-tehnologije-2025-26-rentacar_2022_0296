<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\RentalController;
use App\Http\Controllers\API\StatsController;
use Illuminate\Support\Facades\Route;

// Javne rute (Dostupne svima / Guest)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/locations/{id}', [LocationController::class, 'show']);

// Rute za prijavljene korisnike (Admin i Client)
Route::middleware(['auth'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rezervacije i promena statusa
    Route::apiResource('rentals', RentalController::class);
    Route::patch('/rentals/{id}/status', [RentalController::class, 'changeStatus']);

    // Samo ADMIN ima pristup upravljanju vozilima, lokacijama i statistici
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/locations', [LocationController::class, 'store']);
        Route::put('/locations/{id}', [LocationController::class, 'update']);
        Route::delete('/locations/{id}', [LocationController::class, 'destroy']);

        Route::post('/cars', [CarController::class, 'store']);
        Route::put('/cars/{id}', [CarController::class, 'update']);
        Route::delete('/cars/{id}', [CarController::class, 'destroy']);
        Route::post('/cars/{id}/upload-image', [CarController::class, 'uploadImage']);

        Route::get('/stats/dashboard', [StatsController::class, 'getDashboardStats']);
        Route::get('/stats/export-rentals', [StatsController::class, 'exportRentalsCsv']);
    });
});
