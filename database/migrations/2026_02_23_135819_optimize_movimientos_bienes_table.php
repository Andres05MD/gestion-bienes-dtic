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
        Schema::table('movimientos_bienes', function (Blueprint $table) {
            $table->index(['bien_type', 'bien_id', 'id']);
            $table->index(['departamento_origen_id', 'departamento_destino_id'], 'idx_departamentos_mov');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_bienes', function (Blueprint $table) {
            $table->dropIndex(['bien_type', 'bien_id', 'id']);
            $table->dropIndex('idx_departamentos_mov');
        });
    }
};
