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
        Schema::table('puestos', function (Blueprint $table) {
            if (!Schema::hasColumn('puestos', 'nombre')) {
                $table->string('nombre')->unique()->after('id');
            }
            if (!Schema::hasColumn('puestos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puestos', function (Blueprint $table) {
            if (Schema::hasColumn('puestos', 'nombre')) {
                $table->dropColumn('nombre');
            }
            if (Schema::hasColumn('puestos', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });
    }
};
