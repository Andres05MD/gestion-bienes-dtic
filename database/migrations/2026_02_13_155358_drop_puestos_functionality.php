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
            $table->dropForeign(['puesto_id']);
            $table->dropColumn('puesto_id');
        });
        Schema::dropIfExists('puestos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('bienes', function (Blueprint $table) {
            $table->foreignId('puesto_id')->nullable()->after('area_id')->constrained('puestos')->nullOnDelete();
        });
    }
};
