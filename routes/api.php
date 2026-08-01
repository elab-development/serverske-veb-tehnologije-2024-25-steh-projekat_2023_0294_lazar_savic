<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NekretninaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/nekretnine', [NekretninaController::class, 'index']);
Route::get('/nekretnine/{id}', [NekretninaController::class, 'show']);
Route::get('/nekretnine-pretraga', [NekretninaController::class, 'pretraga']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/nekretnine', [NekretninaController::class, 'store']);
    Route::put('/nekretnine/{id}', [NekretninaController::class, 'update']);
    Route::delete('/nekretnine/{id}', [NekretninaController::class, 'destroy']);
});