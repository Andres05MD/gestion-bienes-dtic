<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Elimina los registros soft-deleted y remueve la columna deleted_at
     * de todas las tablas que la utilizaban.
     */
    public function up(): void
    {
        // Primero, eliminar permanentemente los registros que estaban soft-deleted
        DB::table('bienes')->whereNotNull('deleted_at')->delete();
        DB::table('areas')->whereNotNull('deleted_at')->delete();
        DB::table('estados')->whereNotNull('deleted_at')->delete();
        DB::table('categoria_bienes')->whereNotNull('deleted_at')->delete();

        // Luego, eliminar la columna deleted_at de cada tabla
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('estados', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categoria_bienes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Revierte los cambios restaurando la columna deleted_at.
     */
    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('estados', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('categoria_bienes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
