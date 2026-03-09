<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * prod_hora con 5 decimales para mayor precisión en el cálculo de meta diaria
     * (ej: meta 30/día, jornada 9,5 hrs → prod_hora ≈ 3,15789 para que dé 100%).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE skus MODIFY prod_hora DECIMAL(12, 5) NOT NULL DEFAULT 0');
        } else {
            Schema::table('skus', function (Blueprint $table) {
                $table->decimal('prod_hora', 12, 5)->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE skus MODIFY prod_hora DECIMAL(10, 2) NOT NULL DEFAULT 0');
        } else {
            Schema::table('skus', function (Blueprint $table) {
                $table->decimal('prod_hora', 10, 2)->default(0)->change();
            });
        }
    }
};
