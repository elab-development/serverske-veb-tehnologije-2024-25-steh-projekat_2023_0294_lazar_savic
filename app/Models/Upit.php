<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upit extends Model
{
    use HasFactory;

    protected $table = 'upiti';

    protected $fillable = [
        'poruka',
        'kontakt_email',
        'status_upita',
        'nekretnina_id',
        'korisnik_id',
    ];

    public function nekretnina()
    {
        return $this->belongsTo(Nekretnina::class, 'nekretnina_id');
    }

    public function korisnik()
    {
        return $this->belongsTo(User::class, 'korisnik_id');
    }
}