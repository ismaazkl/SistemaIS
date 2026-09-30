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
        Schema::table('cursos', function (Blueprint $table) {
            $table->foreignId('team_id')->after('id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('fichas', function (Blueprint $table) {
            $table->foreignId('curso_id')->after('id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('fichas', function (Blueprint $table) {
            $table->foreignId('team_id')->after('curso_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->unique(['team_id', 'curso']);
        });

        Schema::table('fichas', function (Blueprint $table) {
            $table->unique(['team_id', 'cedula_estudiante']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'cedula_estudiante']);
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn('curso_id');
            $table->dropColumn('team_id');
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'curso']);
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
