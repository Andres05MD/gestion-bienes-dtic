<?php

declare(strict_types=1);

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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bien');
            $table->string('descripcion');
            $table->string('serial')->nullable();

            // Origen y destino (Similar a transferencias internas, el destino será DTIC para entrada)
            $table->foreignId('procedencia_id')->nullable()->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('destino_id')->nullable()->constrained('departamentos')->cascadeOnDelete();

            // Área de destino en DTIC (y posible área de procedencia)
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('area_procedencia_id')->nullable()->constrained('areas')->nullOnDelete();

            $table->date('fecha');
            $table->foreignId('estatus_acta_id')->nullable()->constrained('estatus_actas')->cascadeOnDelete();
            $table->date('fecha_firma')->nullable();

            // Nuevos campos específicos para Mantenimiento
            $table->string('n_orden_acta')->nullable();
            $table->date('fecha_acta')->nullable();

            // Tipo de movimiento (Entrada a Mantenimiento o Salida hacia Origen)
            $table->enum('tipo_movimiento', ['entrada', 'salida'])->default('entrada');

            // Bienes asociados
            $table->foreignId('bien_id')->nullable()->constrained('bienes')->nullOnDelete();
            $table->foreignId('bien_externo_id')->nullable()->constrained('bienes_externos')->nullOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
