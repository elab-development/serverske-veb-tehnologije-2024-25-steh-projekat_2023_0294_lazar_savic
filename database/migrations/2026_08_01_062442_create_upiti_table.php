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
        Schema::create('upiti', function (Blueprint $table) {
            $table->id();

            $table->text('poruka');
            $table->string('kontakt_email')->nullable();
            $table->enum('status_upita', ['neobradjeno', 'u_obradi', 'zavrseno'])->default('neobradjeno');

            // Foreign keys
            $table->foreignId('nekretnina_id')->constrained('nekretnine')->onDelete('cascade');
            $table->foreignId('korisnik_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upiti');
    }
};
