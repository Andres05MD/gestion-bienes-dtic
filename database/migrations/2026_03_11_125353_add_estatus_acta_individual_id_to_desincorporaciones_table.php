<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración — Agrega estatus de acta individual a desincorporaciones.
     */
    public function up(): void
    {
        Schema::table('desincorporaciones', function (Blueprint $table) {
            $table->foreignId('estatus_acta_individual_id')
                ->nullable()
                ->after('estatus_acta_id')
                ->constrained('estatus_actas')
                ->nullOnDelete();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('desincorporaciones', function (Blueprint $table) {
            $table->dropForeign(['estatus_acta_individual_id']);
            $table->dropColumn('estatus_acta_individual_id');
        });
    }
};
