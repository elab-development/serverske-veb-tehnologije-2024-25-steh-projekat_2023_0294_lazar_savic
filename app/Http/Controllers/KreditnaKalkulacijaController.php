<?php

namespace App\Http\Controllers;

use App\Models\KreditnaKalkulacija;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\KreditnaKalkulacijaResource;

class KreditnaKalkulacijaController extends Controller
{
    // GET /api/kalkulacije - Sacuvane kalkulacije

    public function index(Request $request)
    {
        $kalkulacije = KreditnaKalkulacija::where('korisnik_id', $request->user()->id)
            ->with('nekretnina')
            ->get();

        return response()->json([
            'status' => true,
            'podaci' => KreditnaKalkulacijaResource::collection($kalkulacije)
        ], 200);
    }

    // POST /api/kalkulacije - Sacuvaj i izracunaj ratu kredita

    public function izracunaj(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'iznos_kredita' => 'required|numeric|min:1000',
            'ucesce' => 'required|numeric|min:0',
            'godisnja_kamata' => 'required|numeric|min:0.1|max:20',
            'period_otplate_kredita' => 'required|integer|min:1|max:40',
            'nekretnina_id' => 'nullable|exists:nekretnine,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greška pri validaciji',
                'greske' => $validator->errors()
            ], 422);
        }

        $glavnica = $request->iznos_kredita - $request->ucesce;
        $mesecnaKamatnaStopa = ($request->godisnja_kamata / 100) / 12;
        $brojMeseci = $request->period_otplate_kredita * 12;

        if ($mesecnaKamatnaStopa > 0) {
            $mesecnaRata = $glavnica * ($mesecnaKamatnaStopa * pow(1 + $mesecnaKamatnaStopa, $brojMeseci))
                / (pow(1 + $mesecnaKamatnaStopa, $brojMeseci) - 1);
        } else {
            $mesecnaRata = $glavnica / $brojMeseci;
        }

        $mesecnaRata = round($mesecnaRata, 2);

        $kalkulacija = KreditnaKalkulacija::create([
            'iznos_kredita' => $request->iznos_kredita,
            'ucesce' => $request->ucesce,
            'godisnja_kamata' => $request->godisnja_kamata,
            'period_otplate_kredita' => $request->period_otplate_kredita,
            'mesecna_rata' => $mesecnaRata,
            'nekretnina_id' => $request->nekretnina_id,
            'korisnik_id' => $request->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'poruka' => 'Kalkulacija uspešno izračunata i sačuvana',
            'podaci' => new KreditnaKalkulacijaResource($kalkulacija),
        ], 201);
    }
}
