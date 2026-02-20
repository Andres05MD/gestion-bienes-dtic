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
        Schema::table('transferencias_internas', function (Blueprint $table) {
            $table->string('codigo_acta')->after('id')->nullable()->comment('Agrupa multiples transferencias bajo un mismo código de transacción');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transferencias_internas', function (Blueprint $table) {
            $table->dropColumn('codigo_acta');
        });
    }
};
