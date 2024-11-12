<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\HorarioSalaController;

Route::middleware('api')->group(function () {
    // Rutas para ingresos
    Route::prefix('ingresos')->group(function () {
        Route::post('/', [IngresoController::class, 'store']);
        Route::put('/{id}/salida', [IngresoController::class, 'updateSalida']);
        Route::get('/dia', [IngresoController::class, 'ingresosDelDia']);
        Route::get('/consulta', [IngresoController::class, 'consultarIngresos']);
        Route::put('/{id}', [IngresoController::class, 'updateIngreso']);
    });

    // Aquí podrías agregar rutas para otros controladores, como HorarioSalaController si es necesario.
});

// Ruta para obtener información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
