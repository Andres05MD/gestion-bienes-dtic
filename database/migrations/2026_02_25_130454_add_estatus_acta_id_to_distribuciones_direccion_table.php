<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribuciones_direccion', function (Blueprint $table) {
            $table->foreignId('estatus_acta_id')
                ->nullable()
                ->after('area_id')
                ->constrained('estatus_actas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('distribuciones_direccion', function (Blueprint $table) {
            $table->dropForeign(['estatus_acta_id']);
            $table->dropColumn('estatus_acta_id');
        });
    }
};
