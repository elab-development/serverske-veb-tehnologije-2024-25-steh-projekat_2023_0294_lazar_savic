<?php

namespace App\Policies;

use App\Models\Nekretnina;
use App\Models\User;

class NekretninaPolicy
{
    // Admin ima pun pristup svim akcijama
    public function before(User $user, string $ability): ?bool
    {
        if ($user->uloga === 'admin') {
            return true;
        }

        return null;
    }

    // Izmena nekretnine: Dozvoljeno samo vlasniku koji je stvorio oglas
    public function update(User $user, Nekretnina $nekretnina): bool
    {
        return $user->id === $nekretnina->korisnik_id;
    }

    // Brisanje nekretnine: Dozvoljeno samo vlasniku ili adminu (preko before)
    public function delete(User $user, Nekretnina $nekretnina): bool
    {
        return $user->id === $nekretnina->korisnik_id;
    }
}