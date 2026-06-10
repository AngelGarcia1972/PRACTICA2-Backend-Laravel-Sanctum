<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\V2\ProductoController as V2ProductoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::get('/productos',       [ProductoController::class, 'index']);
    Route::get('/productos/{id}',  [ProductoController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
        Route::apiResource('productos', ProductoController::class)->except(['index', 'show']);
        Route::apiResource('pedidos',   PedidoController::class);
    });
});