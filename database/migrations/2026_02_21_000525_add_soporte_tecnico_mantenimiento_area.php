<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Area::firstOrCreate(['nombre' => 'Soporte Técnico - Mantenimiento']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: Eliminar el área, aunque podría estar en uso, por ello lo dejamos comentado
        // Area::where('nombre', 'Soporte Técnico - Mantenimiento')->delete();
    }
};
