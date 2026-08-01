<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nekretnine', function (Blueprint $table) {
            $table->id();

            $table->string('naslov');
            $table->text('opis');
            $table->decimal('cena', 12, 2);
            $table->integer('kvadratura');
            $table->string('adresa');
            $table->enum('tip', ['stan', 'kuca', 'poslovni_prostor', 'zemljiste']);
            $table->enum('status', ['prodaja', 'izdavanje']);
            $table->boolean('is_istaknuto')->default(false);
            $table->string('slika_putanja')->nullable();

            // Foreign keys
            $table->foreignId('grad_id')->constrained('gradovi')->onDelete('cascade');
            $table->foreignId('korisnik_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nekretnine');
    }
};
