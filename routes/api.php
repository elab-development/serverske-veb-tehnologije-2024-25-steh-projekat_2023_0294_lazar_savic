<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NekretninaController;
use App\Http\Controllers\GradController;
use App\Http\Controllers\UpitController;
use App\Http\Controllers\KreditnaKalkulacijaController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ExternalApiController;

/*
|--------------------------------------------------------------------------
| Javne Rute (Dostupne svima)
|--------------------------------------------------------------------------
*/

// Autentifikacija i Password Reset
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetToken']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Pregled i Pretraga Nekretnina
Route::get('/nekretnine', [NekretninaController::class, 'index']);
Route::get('/nekretnine/pretraga', [NekretninaController::class, 'pretraga']);
Route::get('/nekretnine/{id}', [NekretninaController::class, 'show']);

// Gradovi
Route::get('/gradovi', [GradController::class, 'index']);
Route::get('/gradovi/statistika', [GradController::class, 'statistika']);
Route::get('/gradovi/{id}/nekretnine', [GradController::class, 'nekretnineUGradu']);
Route::get('/gradovi/analitika-trzista', [GradController::class, 'analitikaTrzista']);

// Eksterni API Servisi
Route::get('/nekretnine/{id}/konvertuj-cenu', [ExternalApiController::class, 'konvertujCenu']);
Route::get('/vremenska-prognoza', [ExternalApiController::class, 'vremenskaPrognoza']);


/*
|--------------------------------------------------------------------------
| Zaštićene Rute (Zahtevaju Sanctum Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Odjava
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- ULOGA: KORISNIK (Svi ulogovani korisnici) ---
    Route::get('/kalkulacije', [KreditnaKalkulacijaController::class, 'index']);
    Route::post('/kalkulacije/izracunaj', [KreditnaKalkulacijaController::class, 'izracunaj']);
    Route::post('/upiti', [UpitController::class, 'store']);
    Route::get('/nekretnine/{id}/upiti', [NekretninaController::class, 'upitiZaNekretninu']);

    // --- ULOGA: AGENT I ADMIN (Kreiranje i izmena nekretnina, obrada upita) ---
    Route::middleware('role:agent,admin')->group(function () {
        Route::post('/nekretnine', [NekretninaController::class, 'store']);
        Route::put('/nekretnine/{id}', [NekretninaController::class, 'update']);
        Route::get('/upiti', [UpitController::class, 'index']);
        Route::patch('/upiti/{id}/status', [UpitController::class, 'promeniStatus']);
    });

    // --- ULOGA: ADMIN (Brisanje nekretnina, upravljanje gradovima) ---
    Route::middleware('role:admin')->group(function () {
        Route::delete('/nekretnine/{id}', [NekretninaController::class, 'destroy']);
        Route::post('/gradovi', [GradController::class, 'store']);
        Route::delete('/gradovi/{id}', [GradController::class, 'destroy']);
    });

});