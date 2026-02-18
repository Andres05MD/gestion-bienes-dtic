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
        Schema::table('desincorporaciones', function (Blueprint $table) {
            $table->dropColumn('estatus');
            $table->foreignId('estatus_acta_id')->nullable()->constrained('estatus_actas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desincorporaciones', function (Blueprint $table) {
            $table->dropForeign(['estatus_acta_id']);
            $table->dropColumn('estatus_acta_id');
            $table->string('estatus')->nullable();
        });
    }
};
