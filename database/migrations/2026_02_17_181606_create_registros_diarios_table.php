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
        Schema::create('registros_diarios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('colaborador_id')->constrained('colaboradores')->onDelete('cascade');
            $table->foreignId('sku_id')->constrained('skus')->onDelete('cascade');
            $table->integer('procesadas')->default(0);
            $table->decimal('meta_establecida', 10, 2)->default(0);
            $table->decimal('productividad', 10, 2)->default(0);
            $table->string('hora_ingreso', 5); // Formato HH:MM
            $table->string('hora_finalizacion', 5); // Formato HH:MM
            $table->decimal('horas_proceso', 5, 2)->default(0);
            $table->timestamps();

            $table->index('fecha');
            $table->index('colaborador_id');
            $table->index('sku_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_diarios');
    }
};
