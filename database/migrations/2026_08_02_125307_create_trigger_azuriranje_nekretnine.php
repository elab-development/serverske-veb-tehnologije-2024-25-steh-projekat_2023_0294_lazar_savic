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
        DB::unprepared('
                CREATE TRIGGER trg_pre_azuriranja_nekretnine
                BEFORE UPDATE ON nekretnine
                FOR EACH ROW
                BEGIN
                    SET NEW.updated_at = NOW();
                END
            ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pre_azuriranja_nekretnine');
    }
};
