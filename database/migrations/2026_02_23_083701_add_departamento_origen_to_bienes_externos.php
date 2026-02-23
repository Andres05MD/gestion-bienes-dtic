<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega el campo departamento_origen_id a bienes_externos
     * para mantener la trazabilidad de bienes que vinieron de DTIC.
     */
    public function up(): void
    {
        Schema::table('bienes_externos', function (Blueprint $table) {
            $table->foreignId('departamento_origen_id')
                ->nullable()
                ->after('departamento_id')
                ->constrained('departamentos')
                ->nullOnDelete();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('bienes_externos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('departamento_origen_id');
        });
    }
};
