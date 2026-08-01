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
        Schema::create('kreditne_kalkulacije', function (Blueprint $table) {
            $table->id();

            $table->decimal('iznos_kredita', 12, 2);
            $table->decimal('ucesce', 12, 2);
            $table->decimal('godisnja_kamata', 5, 2);
            $table->integer('period_otplate_kredita');
            $table->decimal('mesecna_rata', 10, 2);
            $table->string('valuta', 3)->default('EUR');

            // Foreign key
            $table->foreignId('korisnik_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kreditne_kalkulacije');
    }
};
