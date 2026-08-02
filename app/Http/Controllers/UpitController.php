<?php

namespace App\Http\Controllers;

use App\Http\Resources\UpitResource;
use App\Models\Upit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpitController extends Controller
{
    // GET /api/upiti - Select All

    public function index()
    {
        $upiti = Upit::with(['nekretnina', 'korisnik'])->paginate(15);

        return response()->json([
            'status' => true,
            'podaci' => UpitResource::collection($upiti),
            'meta' => [
                'trenutna_stranica' => $upiti->currentPage(),
                'ukupno_stranica' => $upiti->lastPage(),
                'ukupno_stavki' => $upiti->total(),
            ]
        ], 200);
    }

    // POST api/upiti - Create One

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poruka' => 'required|string|min:10',
            'nekretnina_id' => 'required|exists:nekretnine,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greška pri validaciji',
                'greske' => $validator->errors()
            ], 422);
        }

        $upit = Upit::create([
            'poruka' => $request->poruka,
            'status_upita' => 'neobradjeno',
            'nekretnina_id' => $request->nekretnina_id,
            'korisnik_id' => $request->user()->id,
        ]);

        $upit->load(['nekretnina', 'korisnik']);

        return response()->json([
            'status' => true,
            'poruka' => 'Upit uspešno poslat',
            'podaci' => new UpitResource($upit)
        ], 201);
    }

    // PUT /api/upiti/{id}/status - Izmena statusa

    public function promeniStatus(Request $request, $id)
    {
        $upit = Upit::find($id);

        if (!$upit) {
            return response()->json([
                'status' => false,
                'poruka' => 'Upit nije pronađen'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status_upita' => 'required|in:neobradjeno,u_obradi,zavrseno',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greška pri validaciji',
                'greske' => $validator->errors()
            ], 422);
        }

        $upit->update(['status_upita' => $request->status_upita]);
        $upit->load(['nekretnina', 'korisnik']);

        return response()->json([
            'status' => true,
            'poruka' => 'Status upita uspešno izmenjen',
            'podaci' => new UpitResource($upit)
        ], 200);
    }
}
