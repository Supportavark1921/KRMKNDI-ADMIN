<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\OpenApiController;
use App\Http\Controllers\Api\PanchangController;
use App\Http\Controllers\Api\PromotionsApiController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/openapi.json', [OpenApiController::class, 'spec']);

// Panchang
Route::get('/v1/panchang', [PanchangController::class, 'show']);

// Promotions
Route::get('/v1/promotions', [PromotionsApiController::class, 'index']);

// Availability
Route::get('/availability/{month}', [AvailabilityController::class, 'month']);
Route::get('/availability/{date}/slots', [AvailabilityController::class, 'slots']);

// Services
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

// Gurus & donations
Route::get('/gurus', [GuruController::class, 'index']);
Route::get('/gurus/{guru}', [GuruController::class, 'show']);
Route::get('/gurus/{guru}/donation-categories', [GuruController::class, 'categories']);
Route::get('/donation/fee-config', [DonationController::class, 'feeConfig']);
Route::post('/donations', [DonationController::class, 'store']);
