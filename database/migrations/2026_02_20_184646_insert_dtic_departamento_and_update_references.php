<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asegurar que exista un departamento llamado "DTIC" 
        // e identificar su ID (o crearlo). Usamos DB query builder para usar Raw SQL directo.

        $dtic = DB::table('departamentos')
            ->where('nombre', 'DTIC')
            ->first();

        $dticId = null;

        if (!$dtic) {
            $dticId = DB::table('departamentos')->insertGetId([
                'nombre' => 'DTIC',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $dticId = $dtic->id;
        }

        // 2. Actualizar registros huérfanos con valor null a este nuevo ID de DTIC en las 3 tablas transaccionales.

        if ($dticId) {
            DB::table('desincorporaciones')
                ->whereNull('procedencia_id')
                ->update(['procedencia_id' => $dticId]);

            DB::table('desincorporaciones')
                ->whereNull('destino_id')
                ->update(['destino_id' => $dticId]);

            DB::table('distribuciones_direccion')
                ->whereNull('procedencia_id')
                ->update(['procedencia_id' => $dticId]);

            DB::table('transferencias_internas')
                ->whereNull('procedencia_id')
                ->update(['procedencia_id' => $dticId]);

            DB::table('transferencias_internas')
                ->whereNull('destino_id')
                ->update(['destino_id' => $dticId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir no elimina el departamento por seguridad (podría tener otras dependencias),
        // pero sí vuelve nulos a los que apunten a este.
        $dtic = DB::table('departamentos')->where('nombre', 'DTIC')->first();

        if ($dtic) {
            $dticId = $dtic->id;

            DB::table('desincorporaciones')->where('procedencia_id', $dticId)->update(['procedencia_id' => null]);
            DB::table('desincorporaciones')->where('destino_id', $dticId)->update(['destino_id' => null]);
            DB::table('distribuciones_direccion')->where('procedencia_id', $dticId)->update(['procedencia_id' => null]);
            DB::table('transferencias_internas')->where('procedencia_id', $dticId)->update(['procedencia_id' => null]);
            DB::table('transferencias_internas')->where('destino_id', $dticId)->update(['destino_id' => null]);

            DB::table('departamentos')->where('id', $dticId)->delete();
        }
    }
};
