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
        Schema::create('bienes_externos', function (Blueprint $table) {
            $table->id();
            $table->string('equipo');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('serial')->nullable();
            $table->string('color')->nullable();
            $table->string('numero_bien')->unique();
            $table->text('observaciones')->nullable();
            
            // Relaciones
            $table->foreignId('categoria_bien_id')->constrained('categoria_bienes');
            $table->foreignId('estado_id')->constrained('estados');
            $table->foreignId('departamento_id')->constrained('departamentos'); // En lugar de areas
            $table->foreignId('user_id')->constrained('users'); // Usuario que registró
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bienes_externos');
    }
};
