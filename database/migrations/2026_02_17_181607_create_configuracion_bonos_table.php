<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_bonos', function (Blueprint $table) {
            $table->id();
            // Montos para Líder
            $table->decimal('lider_productividad', 10, 2)->default(70000); // $70,000 (70%)
            $table->decimal('lider_asistencia', 10, 2)->default(30000); // $30,000 (30%)
            // Montos para Ayudante
            $table->decimal('ayudante_productividad', 10, 2)->default(45000); // $45,000 (70%)
            $table->decimal('ayudante_asistencia', 10, 2)->default(19500); // $19,500 (30%)
            // Factores de cumplimiento
            $table->decimal('factor_verde', 5, 2)->default(1.2);
            $table->decimal('factor_amarillo', 5, 2)->default(0.8);
            $table->decimal('factor_rojo', 5, 2)->default(0.5);
            // Límites para semáforo productividad
            $table->decimal('meta_productividad', 5, 2)->default(100); // Meta 100%
            $table->decimal('limite_amarillo_prod', 5, 2)->default(80); // >= 80% y < 100% = Amarillo
            // Límites para semáforo asistencia
            $table->decimal('meta_asistencia', 5, 2)->default(0); // Meta 0%
            $table->decimal('limite_amarillo_asist', 5, 2)->default(-5); // >= -5% y < 0% = Amarillo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_bonos');
    }
};
