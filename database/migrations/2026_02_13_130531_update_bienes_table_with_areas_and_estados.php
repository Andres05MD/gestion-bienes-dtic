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
        Schema::table('bienes', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('set null');
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null');
            $table->dropColumn(['ubicacion', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['estado_id']);
            $table->string('ubicacion')->nullable();
            $table->string('estado')->nullable();
        });
    }
};
