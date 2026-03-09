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
        Schema::table('configuracion_bonos', function (Blueprint $table) {
            $table->decimal('descuento_atraso', 5, 2)->default(-2)->after('limite_amarillo_asist')->comment('% informativo para registro de asistencia');
            $table->decimal('descuento_ausencia', 5, 2)->default(-5)->after('descuento_atraso')->comment('% informativo para registro de asistencia');
            $table->decimal('horas_por_dia', 5, 2)->default(9.5)->after('descuento_ausencia')->comment('Horas consideradas por día de trabajo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracion_bonos', function (Blueprint $table) {
            $table->dropColumn(['descuento_atraso', 'descuento_ausencia', 'horas_por_dia']);
        });
    }
};
