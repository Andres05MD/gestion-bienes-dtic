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
        if (!Schema::hasColumn('categoria_bienes', 'deleted_at')) {
            Schema::table('categoria_bienes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('areas', 'deleted_at')) {
            Schema::table('areas', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        if (!Schema::hasColumn('estados', 'deleted_at')) {
            Schema::table('estados', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categoria_bienes', 'deleted_at')) {
            Schema::table('categoria_bienes', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        if (Schema::hasColumn('areas', 'deleted_at')) {
            Schema::table('areas', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
        if (Schema::hasColumn('estados', 'deleted_at')) {
            Schema::table('estados', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
