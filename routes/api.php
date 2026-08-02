<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradController;
use App\Http\Controllers\KreditnaKalkulacijaController;
use App\Http\Controllers\NekretninaController;
use App\Http\Controllers\UpitController;
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

Route::get("/gradovi", [GradController::class, 'index']);
Route::get("/gradovi/{id}", [GradController::class, 'nekretnineUGradu']);
Route::get("/gradovi-statistika", [GradController::class, 'statistika']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/nekretnine', [NekretninaController::class, 'store']);
    Route::put('/nekretnine/{id}', [NekretninaController::class, 'update']);
    Route::delete('/nekretnine/{id}', [NekretninaController::class, 'destroy']);

    Route::post('/gradovi', [GradController::class, 'store']);

    Route::get('/upiti', [UpitController::class, 'index']);
    Route::post('/upiti', [UpitController::class, 'store']);
    Route::put('/upiti/{id}/status', [UpitController::class, 'promeniStatus']);

    Route::get('/kalkulacije', [KreditnaKalkulacijaController::class, 'index']);
    Route::post('/kalkulacije', [KreditnaKalkulacijaController::class, 'izracunaj']);
});