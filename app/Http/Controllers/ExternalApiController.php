<?php

namespace App\Http\Controllers;

use App\Models\Nekretnina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExternalApiController extends Controller
{
    // Javni servis 1: Konverzija cene nekretnine u druge valute
    public function konvertujCenu(Request $request, $id)
    {
        $nekretnina = Nekretnina::find($id);

        if (!$nekretnina) {
            return response()->json([
                'status' => false,
                'poruka' => 'Nekretnina nije pronađena.'
            ], 404);
        }

        $response = Http::get('https://api.frankfurter.app/latest', [
            'amount' => $nekretnina->cena,
            'from' => 'EUR',
            'to' => 'USD,CHF,GBP'
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greška prilikom pozivanja eksternog servisa za kursnu listu.'
            ], 500);
        }

        $kursnaLista = $response->json();

        return response()->json([
            'status' => true,
            'nekretnina_id' => $nekretnina->id,
            'osnovna_cena_eur' => $nekretnina->cena,
            'preračunate_valute' => $kursnaLista['rates']
        ], 200);
    }

    // Javni servis 2: Vremenska prognoza za Beograd
    public function vremenskaPrognoza()
    {
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => 44.8176,
            'longitude' => 20.4633,
            'current_weather' => true
        ]);

        if ($response->failed()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greška prilikom dobijanja podataka o vremenskoj prognozi.'
            ], 500);
        }

        $vreme = $response->json()['current_weather'];

        return response()->json([
            'status' => true,
            'lokacija' => 'Beograd',
            'temperatura' => $vreme['temperature'] . ' °C',
            'brzina_vetra' => $vreme['windspeed'] . ' km/h',
            'vreme_merenja' => $vreme['time']
        ], 200);
    }
}