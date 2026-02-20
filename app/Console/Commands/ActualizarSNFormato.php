<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bien;
use App\Models\BienExterno;
use Illuminate\Support\Str;

class ActualizarSNFormato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bienes:actualizar-sn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte los códigos S/N antiguos basados en uniqid() a S/N secuenciales amigables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando la actualización de formato S/N...');

        // Identificar los bienes que tengan formato extraño
        // (los que tienen S/N- y luego algo que no sea numérico o que sean muy largos).
        // En la base de datos MySQL, "NOT REGEXP '^[0-9]+$'" o simplemente traemos los que tengan longitud > 10.
        // Ej: 'S/N-69975723A64DA' tiene 17 caracteres.

        $bienes = Bien::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8') // S/N-001 (7 chars) -> por seguridad mayores a 8
            ->get();

        $contadorBienes = 0;
        foreach ($bienes as $bien) {
            $nuevoCodigo = Bien::generarNumeroSN();
            $bien->update(['numero_bien' => $nuevoCodigo]);
            $contadorBienes++;
        }

        $bienesExternos = BienExterno::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8')
            ->get();

        $contadorBienesExternos = 0;
        foreach ($bienesExternos as $bienExt) {
            $nuevoCodigo = BienExterno::generarNumeroSN();
            $bienExt->update(['numero_bien' => $nuevoCodigo]);
            $contadorBienesExternos++;
        }

        // --- ACTUALIZACIÓN DE OPERACIONES HUÉRFANAS ---
        $contadorOperaciones = 0;

        $transferencias = \App\Models\TransferenciaInterna::whereRaw('LENGTH(numero_bien) > 8')->get();
        foreach ($transferencias as $op) {
            $op->update(['numero_bien' => Bien::generarNumeroSN()]);
            $contadorOperaciones++;
        }

        $desincorporaciones = \App\Models\Desincorporacion::whereRaw('LENGTH(numero_bien) > 8')->get();
        foreach ($desincorporaciones as $op) {
            $op->update(['numero_bien' => Bien::generarNumeroSN()]);
            $contadorOperaciones++;
        }

        $distribuciones = \App\Models\DistribucionDireccion::whereRaw('LENGTH(numero_bien) > 8')->get();
        foreach ($distribuciones as $op) {
            $op->update(['numero_bien' => Bien::generarNumeroSN()]);
            $contadorOperaciones++;
        }

        $this->info("Proceso finalizado.");
        $this->info("- Bienes DTIC actualizados: {$contadorBienes}");
        $this->info("- Bienes Externos actualizados: {$contadorBienesExternos}");
        $this->info("- Operaciones huérfanas actualizadas: {$contadorOperaciones}");
    }
}
