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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->integer('semana'); // Número de semana del año (1-52)
            $table->integer('anio');
            $table->foreignId('colaborador_id')->constrained('colaboradores')->onDelete('cascade');
            $table->decimal('ausencia', 5, 2)->default(0); // Porcentaje de ausencia
            $table->decimal('atraso', 5, 2)->default(0); // Porcentaje de atraso
            $table->decimal('total', 5, 2)->default(0); // Total = ausencia + atraso
            $table->timestamps();

            $table->unique(['semana', 'anio', 'colaborador_id']);
            $table->index('colaborador_id');
            $table->index(['semana', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
