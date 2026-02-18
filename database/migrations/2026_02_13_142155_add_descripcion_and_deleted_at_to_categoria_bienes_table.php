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
        Schema::table('categoria_bienes', function (Blueprint $table) {
            if (!Schema::hasColumn('categoria_bienes', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoria_bienes', function (Blueprint $table) {
            if (Schema::hasColumn('categoria_bienes', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });
    }
};
