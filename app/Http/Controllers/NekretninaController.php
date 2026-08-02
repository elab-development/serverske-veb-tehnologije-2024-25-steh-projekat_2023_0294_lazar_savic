<?php

namespace App\Http\Controllers;

use App\Http\Resources\NekretninaResource;
use App\Http\Resources\UpitResource;
use App\Models\Nekretnina;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NekretninaController extends Controller
{
    // GET /api/nekretnine - Show All

    public function index()
    {
        $nekretnine = Nekretnina::with(['grad', 'korisnik'])->paginate(10);

        return response()->json([
            'status' => true,
            'podaci' => NekretninaResource::collection($nekretnine),
            'meta' => [
                'trenutna_stranica' => $nekretnine->currentPage(),
                'ukupno_stranica' => $nekretnine->lastPage(),
                'ukupno_stavki' => $nekretnine->total(),
            ]
        ], 200);
    }

    // POST /api/nekretnine - Create New

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'naslov' => 'required|string|max:255',
            'opis' => 'required|string',
            'cena' => 'required|numeric|min:0',
            'popust' => 'nullable|numeric|min:0|max:100',
            'kvadratura' => 'required|integer|min:1',
            'adresa' => 'required|string',
            'tip' => 'required|in:stan,kuca,poslovni_prostor,zemljiste',
            'status' => 'required|in:prodaja,izdavanje',
            'is_istaknuto' => 'boolean',
            'grad_id' => 'required|exists:gradovi,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greska pri validaciji.',
                'greske' => $validator->errors(),
            ], 422);
        }

        // DB Transakcija 
        $nekretnina = DB::transaction(function () use ($request) {
            return Nekretnina::create([
                'naslov' => $request->naslov,
                'opis' => $request->opis,
                'cena' => $request->cena,
                'popust' => $request->popust ?? 0,
                'kvadratura' => $request->kvadratura,
                'adresa' => $request->adresa,
                'tip' => $request->tip,
                'status' => $request->status,
                'is_istaknuto' => $request->is_istaknuto ?? false,
                'grad_id' => $request->grad_id,
                'korisnik_id' => $request->user()->id,
            ]);
        });

        return response()->json([
            'status' => true,
            'poruka' => 'Nekretnina uspešno kreirana',
            'podaci' => $nekretnina
        ], 201);
    }

    // GET /api/nekretnine/{id} - Select One

    public function show($id)
    {
        $nekretnina = Nekretnina::with('grad')->find($id);

        if (!$nekretnina) {
            return response()->json([
                'status' => false,
                'poruka' => 'Nekretnina nije pronađena'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'podaci' => new NekretninaResource($nekretnina)
        ], 200);
    }


    // PUT /api/nekretnine/{id} - Update One

    public function update(Request $request, $id)
    {
        $nekretnina = Nekretnina::find($id);

        if (!$nekretnina) {
            return response()->json([
                'status' => false,
                'poruka' => 'Nekretnina nije pronađena'
            ], 404);
        }

        if ($request->user()->cannot('update', $nekretnina)) {
            return response()->json([
                'status' => false,
                'poruka' => 'Pristup odbijen. Niste vlasnik ove nekretnine.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'naslov' => 'sometimes|string|max:255',
            'opis' => 'sometimes|string',
            'cena' => 'sometimes|numeric|min:0',
            'popust' => 'nullable|numeric|min:0|max:100',
            'kvadratura' => 'sometimes|integer|min:1',
            'adresa' => 'sometimes|string',
            'tip' => 'sometimes|in:stan,kuca,poslovni_prostor,zemljiste',
            'status' => 'sometimes|in:prodaja,izdavanje',
            'is_istaknuto' => 'boolean',
            'grad_id' => 'sometimes|exists:gradovi,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greška pri validaciji',
                'greske' => $validator->errors()
            ], 422);
        }

        $nekretnina->update($request->all());

        return response()->json([
            'status' => true,
            'poruka' => 'Nekretnina uspešno ažurirana',
            'podaci' => $nekretnina
        ], 200);
    }

    // DELETE api/nekretnine/{id} - Delete One

    public function destroy(Request $request, $id)
    {
        $nekretnina = Nekretnina::find($id);

        if (!$nekretnina) {
            return response()->json([
                'status' => false,
                'poruka' => 'Nekretnina nije pronađena'
            ], 404);
        }

        if ($request->user()->cannot('delete', $nekretnina)) {
            return response()->json([
                'status' => false,
                'poruka' => 'Pristup odbijen. Niste vlasnik ove nekretnine.'
            ], 403);
        }

        $nekretnina->delete();

        return response()->json([
            'status' => true,
            'poruka' => 'Nekretnina uspešno obrisana'
        ], 200);
    }

    // Filtriranje po tipu i statusu

    public function pretraga(Request $request)
    {
        $query = Nekretnina::query();

        if ($request->has('tip')) {
            $query->where('tip', $request->tip);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('max_cena')) {
            $query->where('cena', '<=', $request->max_cena);
        }

        return response()->json([
            'status' => true,
            'rezultati' => $query->with('grad')->get()
        ], 200);
    }

    // GET /api/nekretnine/{id}/upiti (Ugnježđena ruta za prikaz upita određene nekretnine)
    public function upitiZaNekretninu($id)
    {
        $nekretnina = Nekretnina::find($id);

        if (!$nekretnina) {
            return response()->json([
                'status' => false,
                'poruka' => 'Nekretnina nije pronađena'
            ], 404);
        }

        $upiti = $nekretnina->upiti()->with('korisnik')->get();

        return response()->json([
            'status' => true,
            'nekretnina' => $nekretnina->naslov,
            'broj_upita' => $upiti->count(),
            'podaci' => UpitResource::collection($upiti)
        ], 200);
    }
}
