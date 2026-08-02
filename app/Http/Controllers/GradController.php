<?php

namespace App\Http\Controllers;

use App\Http\Resources\GradResource;
use App\Http\Resources\NekretninaResource;
use App\Models\Grad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GradController extends Controller
{
    // GET /api/gradovi - Select All

    public function index()
    {
        $gradovi = Cache::remember('gradovi_lista', 3600, function () {
            return Grad::all();
        });

        return response()->json([
            'status' => true,
            'podaci' => GradResource::collection($gradovi)
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
                'poruka' => 'Grad nije pronađen'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'grad' => new GradResource($grad),
            'broj_nekretnina' => $grad->nekretnine->count(),
            'nekretnine' => NekretninaResource::collection($grad->nekretnine)
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

    // GET /api/gradovi/analitika-trzista (Složeni SQL upit sa višestrukim JOIN-om i agregacijom)
    public function analitikaTrzista()
    {
        $izvestaj = Cache::remember('gradovi_analitika', 3600, function () {
            return DB::table('gradovi')
                ->join('nekretnine', 'gradovi.id', '=', 'nekretnine.grad_id')
                ->join('users', 'nekretnine.korisnik_id', '=', 'users.id')
                ->leftJoin('upiti', 'nekretnine.id', '=', 'upiti.nekretnina_id')
                ->select(
                    'gradovi.naziv as grad',
                    'gradovi.postanski_broj',
                    DB::raw('COUNT(DISTINCT nekretnine.id) as ukupno_nekretnina'),
                    DB::raw('COUNT(DISTINCT upiti.id) as ukupno_upita'),
                    DB::raw('ROUND(AVG(nekretnine.cena), 2) as prosecna_cena_eur'),
                    DB::raw('ROUND(AVG(nekretnine.cena / nekretnine.kvadratura), 2) as prosecna_cena_po_m2')
                )
                ->groupBy('gradovi.id', 'gradovi.naziv', 'gradovi.postanski_broj')
                ->having('ukupno_nekretnina', '>', 0)
                ->orderByDesc('prosecna_cena_po_m2')
                ->get();
        });

        return response()->json([
            'status' => true,
            'poruka' => 'Izveštaj uspešno generisan.',
            'podaci' => $izvestaj
        ], 200);
    }

    // GET /api/gradovi/export-csv (Eksport analitike u CSV formatu)
    public function exportCsv()
    {
        $gradovi = DB::table('gradovi')
            ->join('nekretnine', 'gradovi.id', '=', 'nekretnine.grad_id')
            ->leftJoin('upiti', 'nekretnine.id', '=', 'upiti.nekretnina_id')
            ->select(
                'gradovi.naziv as grad',
                'gradovi.postanski_broj',
                DB::raw('COUNT(DISTINCT nekretnine.id) as ukupno_nekretnina'),
                DB::raw('COUNT(DISTINCT upiti.id) as ukupno_upita'),
                DB::raw('ROUND(AVG(nekretnine.cena), 2) as prosecna_cena_eur'),
                DB::raw('ROUND(AVG(nekretnine.cena / nekretnine.kvadratura), 2) as prosecna_cena_po_m2')
            )
            ->groupBy('gradovi.id', 'gradovi.naziv', 'gradovi.postanski_broj')
            ->get();

        $fileName = 'analitika_trzista_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($gradovi) {
            $file = fopen('php://output', 'w');

            // Dodavanje UTF-8 BOM za ispravan prikaz ćirilice/latinice u Excel-u
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Imena kolona
            fputcsv($file, ['Grad', 'Poštanski Broj', 'Ukupno Nekretnina', 'Ukupno Upita', 'Prosečna Cena (EUR)', 'Prosečna Cena po m2 (EUR)']);

            foreach ($gradovi as $row) {
                fputcsv($file, [
                    $row->grad,
                    $row->postanski_broj,
                    $row->ukupno_nekretnina,
                    $row->ukupno_upita,
                    $row->prosecna_cena_eur,
                    $row->prosecna_cena_po_m2
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
