<?php

namespace Database\Seeders;

use App\Models\Grad;
use App\Models\Nekretnina;
use App\Models\Upit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'uloga' => 'admin'
        ]);

        $agent = User::create([
            'name' => 'Agent',
            'email' => 'agent@test.com',
            'password' => Hash::make('password123'),
            'uloga' => 'agent'
        ]);

        $klijent = User::create([
            'name' => 'Klijent',
            'email' => 'klijent@test.com',
            'password' => Hash::make('password123'),
            'uloga' => 'klijent'
        ]);

        $gradovi = Grad::factory(5)->create();

        foreach ($gradovi as $grad) {
            Nekretnina::factory(3)->create([
                'grad_id' => $grad->id,
                'korisnik_id' => $agent->id,
            ]);
        }

        $nekretnina = Nekretnina::first();
        if ($nekretnina) {
            Upit::factory(2)->create([
                'nekretnina_id' => $nekretnina->id,
                'korisnik_id' => $klijent->id,
            ]);
        }
    }
}
