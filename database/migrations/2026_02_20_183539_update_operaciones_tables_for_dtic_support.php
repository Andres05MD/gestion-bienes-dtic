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
        // 1. Transferencias Internas
        Schema::table('transferencias_internas', function (Blueprint $table) {
            // Aseguramos que procedencia y destino soporten el valor (DTIC) nulo
            $table->unsignedBigInteger('procedencia_id')->nullable()->change();
            $table->unsignedBigInteger('destino_id')->nullable()->change();

            // Añadir areas
            if (!Schema::hasColumn('transferencias_internas', 'area_procedencia_id')) {
                $table->foreignId('area_procedencia_id')->nullable()->constrained('areas')->nullOnDelete();
            }
            if (!Schema::hasColumn('transferencias_internas', 'area_id')) {
                $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            }
        });

        // 2. Desincorporaciones
        Schema::table('desincorporaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('procedencia_id')->nullable()->change();

            // Resulta que desincorporaciones no tenía destino_id originalmente
            if (!Schema::hasColumn('desincorporaciones', 'destino_id')) {
                $table->foreignId('destino_id')->nullable()->constrained('departamentos')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('destino_id')->nullable()->change();
            }

            if (!Schema::hasColumn('desincorporaciones', 'area_procedencia_id')) {
                $table->foreignId('area_procedencia_id')->nullable()->constrained('areas')->nullOnDelete();
            }
            if (!Schema::hasColumn('desincorporaciones', 'area_id')) {
                $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            }
        });

        // 3. Distribuciones Dirección
        Schema::table('distribuciones_direccion', function (Blueprint $table) {
            $table->unsignedBigInteger('procedencia_id')->nullable()->change();

            if (!Schema::hasColumn('distribuciones_direccion', 'area_id')) {
                // Almacenamos el area de distribución
                $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferencias_internas', function (Blueprint $table) {
            $table->unsignedBigInteger('procedencia_id')->nullable(false)->change();
            $table->unsignedBigInteger('destino_id')->nullable(false)->change();

            if (Schema::hasColumn('transferencias_internas', 'area_procedencia_id')) {
                $table->dropForeign(['area_procedencia_id']);
                $table->dropColumn('area_procedencia_id');
            }
            if (Schema::hasColumn('transferencias_internas', 'area_id')) {
                $table->dropForeign(['area_id']);
                $table->dropColumn('area_id');
            }
        });

        Schema::table('desincorporaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('procedencia_id')->nullable(false)->change();

            if (Schema::hasColumn('desincorporaciones', 'destino_id')) {
                $table->dropForeign(['destino_id']);
                $table->dropColumn('destino_id');
            }
            if (Schema::hasColumn('desincorporaciones', 'area_procedencia_id')) {
                $table->dropForeign(['area_procedencia_id']);
                $table->dropColumn('area_procedencia_id');
            }
            if (Schema::hasColumn('desincorporaciones', 'area_id')) {
                $table->dropForeign(['area_id']);
                $table->dropColumn('area_id');
            }
        });

        Schema::table('distribuciones_direccion', function (Blueprint $table) {
            $table->unsignedBigInteger('procedencia_id')->nullable(false)->change();

            if (Schema::hasColumn('distribuciones_direccion', 'area_id')) {
                $table->dropForeign(['area_id']);
                $table->dropColumn('area_id');
            }
        });
    }
};
