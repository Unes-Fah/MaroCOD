<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\StatsController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders',[OrderController::class, 'store']);
    Route::get('/carriers', [CarrierController::class, 'index']);
    Route::get('/carriers/{carrier}', [CarrierController::class, 'show'])->whereNumber('carrier');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/orders/{order}/carrier', [OrderController::class, 'assignCarrier']);
    Route::get('/stats/status-breakdown', [StatsController::class, 'statusBreakdown']);
    Route::get('/stats/shipping-costs', [StatsController::class, 'shippingCosts']);
    Route::get('/stats/financial-summary', [StatsController::class, 'financialSummary']);
    Route::get('/carriers/cheapest',[CarrierController::class, 'cheapestCarrier']);
    
});
   


