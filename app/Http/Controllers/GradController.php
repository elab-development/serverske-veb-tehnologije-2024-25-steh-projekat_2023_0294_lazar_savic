<?php

namespace App\Http\Controllers;

use App\Models\Grad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradController extends Controller
{
    // GET /api/gradovi - Select All

    public function index()
    {
        $gradovi = Grad::all();

        return response()->json([
            'stats' => true,
            'podaci' => $gradovi,
        ], 200);
    }

    // POST /api/gradovi - Create New

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'naziv' => 'required|string|max:255|unique:gradovi,naziv',
            'postanski_broj' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'poruka' => 'Greska pri validaciji.',
                'greske' => $validator->errors(),
            ], 422);
        }

        $grad = Grad::create($request->all());

        return response()->json([
            'status' => true,
            'poruka' => 'Uspesno kreiran grad.',
            'podaci' => $grad,
        ], 200);
    }

    // GET api/gradovi/{id}/nekretnine - Prikaz nekretnina za odredjeni grad

    public function nekretnineUGradu($id)
    {
        $grad = Grad::with('nekretnine')->find($id);

        if (!$grad) {
            return response()->json([
                'status' => false,
                'poruka' => 'Grad nije pronadjen',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'grad' => $grad->naziv,
            'broj_nekretnina' => $grad->nekretnine->count(),
            'nekretnine' => $grad->nekretnine
        ], 200);
    }

    // GET api/gradovi-statistika - Statistika nekretnina po gradu

    public function statistika()
    {
        $statistika = Grad::withCount('nekretnine')->get(['id', 'naziv', 'postanski_broj']);

        return response()->json([
            'status' => true,
            'podaci' => $statistika
        ], 200);
    }
}
