<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nekretnine extends Model
{
    use HasFactory;

    protected $fillable = [
        'naslov',
        'opis',
        'cena',
        'kvadratura',
        'adresa',
        'tip',
        'status',
        'is_istaknuto',
        'slika_putanja',
        'grad_id',
        'korisnik_id'
    ];

    public function grad()
    {
        return $this->belongsTo(Grad::class, 'grad_id');
    }

    public function korisnik()
    {
        return $this->belongsTo(User::class, 'korisnik_id');
    }

    public function upiti()
    {
        return $this->hasMany(Upit::class, 'nekretnina_id');
    }
}
