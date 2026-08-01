<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grad extends Model
{
    use HasFactory;
    protected $table = 'gradovi';

    protected $fillable = ['naziv', 'postanski_broj'];

    public function nekretnine()
    {
        return $this->hasMany(Nekretnine::class, 'grad_id');
    }
}
