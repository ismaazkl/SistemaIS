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
        Schema::table('fichas', function (Blueprint $table) {
            $table->renameColumn('cedulaRepresentante', 'cedula_representante');
            $table->renameColumn('cedulaEstudiante', 'cedula_estudiante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas', function (Blueprint $table) {
            $table->renameColumn('cedula_representante', 'cedulaRepresentante');
            $table->renameColumn('cedula_estudiante', 'cedulaEstudiante');
        });
    }
};
