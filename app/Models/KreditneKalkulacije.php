<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KreditnaKalkulacija extends Model
{
    use HasFactory;

    protected $fillable = [
        'iznos_kredita',
        'ucesce',
        'godisnja_kamata',
        'period_otplate_godina',
        'mesecna_rata',
        'valuta',
        'korisnik_id',
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class, 'korisnik_id');
    }
}