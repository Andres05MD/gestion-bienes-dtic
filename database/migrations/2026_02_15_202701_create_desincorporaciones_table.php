<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración — Tabla de Desincorporaciones.
     */
    public function up(): void
    {
        Schema::create('desincorporaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bien');
            $table->string('descripcion');
            $table->string('serial')->nullable();
            $table->foreignId('procedencia_id')->constrained('departamentos')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('numero_informe');
            $table->string('estatus');
            $table->text('observaciones')->nullable();
            $table->foreignId('bien_id')->nullable()->constrained('bienes')->nullOnDelete();
            $table->foreignId('bien_externo_id')->nullable()->constrained('bienes_externos')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('desincorporaciones');
    }
};
