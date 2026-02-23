<?php

use App\Http\Controllers\CalculatorController;
use Illuminate\Support\Facades\Route;

Route::prefix('calculations')->group(function () {
    Route::get('/', [CalculatorController::class, 'index']);
    Route::post('/', [CalculatorController::class, 'store']);
    Route::delete('/{id}', [CalculatorController::class, 'destroy']);
    Route::delete('/', [CalculatorController::class, 'clear']);
});
