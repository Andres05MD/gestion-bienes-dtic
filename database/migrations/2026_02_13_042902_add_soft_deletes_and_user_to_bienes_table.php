<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega soft deletes y relación con usuario a la tabla bienes.
     */
    public function up(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->softDeletes();
            $table->foreignId('user_id')->nullable()->after('ubicacion')->constrained()->nullOnDelete();
        });
    }

    /**
     * Revierte los cambios.
     */
    public function down(): void
    {
        Schema::table('bienes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
