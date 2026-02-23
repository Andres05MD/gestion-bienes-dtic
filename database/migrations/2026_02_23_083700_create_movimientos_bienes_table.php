<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración — Tabla de Historial de Movimientos de Bienes.
     */
    public function up(): void
    {
        Schema::create('movimientos_bienes', function (Blueprint $table) {
            $table->id();

            // Referencia al bien (polimórfica: Bien o BienExterno)
            $table->string('bien_type');
            $table->unsignedBigInteger('bien_id');
            $table->string('numero_bien'); // Snapshot del número de bien al momento

            // Tipo de movimiento
            $table->string('tipo_movimiento'); // transferencia, desincorporacion, distribucion, mantenimiento, registro

            // Referencia a la operación que generó el movimiento (polimórfica)
            $table->nullableMorphs('operacion');

            // Ubicación DESDE → HASTA
            $table->foreignId('departamento_origen_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->foreignId('departamento_destino_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->foreignId('area_origen_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('area_destino_id')->nullable()->constrained('areas')->nullOnDelete();

            // Metadata
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['bien_type', 'bien_id']);
            $table->index('tipo_movimiento');
            $table->index('fecha');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_bienes');
    }
};
