<?php

use OpenApi\Generator;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResponseController;


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class,'logout'])->name('logout')->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('response',ResponseController::class);
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/assign/{agent}', [TicketController::class, 'assign']);
});
