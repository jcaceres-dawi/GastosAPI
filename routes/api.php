<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/expenses', [ExpenseController::class, 'index'])->middleware('auth:api');
Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('auth:api');
Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->middleware('auth:api');
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->middleware('auth:api');

Route::get('/expenses/{category}', [ExpenseController::class, 'listCategory'])->middleware('auth:api');
