<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // SUPERVISOR, COLABORADOR
            $table->string('display_name'); // Nombre para mostrar
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insertar roles por defecto
        DB::table('roles')->insert([
            ['name' => 'SUPERVISOR', 'display_name' => 'Supervisor', 'description' => 'Usuario con permisos de administración', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'COLABORADOR', 'display_name' => 'Colaborador', 'description' => 'Usuario colaborador del área de alistamiento', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
