<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\MovimientoBien;
use App\Models\TransferenciaInterna;
use App\Models\Desincorporacion;
use App\Models\DistribucionDireccion;

class ActualizarSNFormato extends Command
{
    protected $signature = 'bienes:actualizar-sn {--force : Forzar re-secuenciación de todos los S/N}';

    protected $description = 'Corrige formatos antiguos y resuelve DUPLICADOS entre tablas DTIC y Externos.';

    private function obtenerMaxSN(): int
    {
        $maxNumero = 0;
        $bienesSN = Bien::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw("numero_bien REGEXP '^S/N-[0-9]+$'")
            ->pluck('numero_bien');

        $externosSN = BienExterno::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw("numero_bien REGEXP '^S/N-[0-9]+$'")
            ->pluck('numero_bien');

        foreach ($bienesSN->merge($externosSN) as $numero) {
            $partes = explode('-', $numero);
            if (isset($partes[1]) && is_numeric($partes[1])) {
                $num = (int) $partes[1];
                if ($num > $maxNumero) {
                    $maxNumero = $num;
                }
            }
        }
        return $maxNumero;
    }

    private function siguienteSN(int &$contador): string
    {
        $contador++;
        return 'S/N-' . str_pad((string) $contador, 3, '0', STR_PAD_LEFT);
    }

    public function handle()
    {
        $this->info('Analizando integridad de números S/N...');

        // 1. Detectar duplicados reales (mismo número en ambas tablas)
        $snDtic = Bien::where('numero_bien', 'LIKE', 'S/N-%')->pluck('numero_bien', 'id')->toArray();
        $snExternos = BienExterno::where('numero_bien', 'LIKE', 'S/N-%')->pluck('numero_bien', 'id')->toArray();

        $duplicadosEncontrados = array_intersect($snDtic, $snExternos);

        // 2. Detectar formatos antiguos (largos)
        $largosDtic = Bien::where('numero_bien', 'LIKE', 'S/N-%')->whereRaw('LENGTH(numero_bien) > 8')->get();
        $largosExternos = BienExterno::where('numero_bien', 'LIKE', 'S/N-%')->whereRaw('LENGTH(numero_bien) > 8')->get();

        if (empty($duplicadosEncontrados) && $largosDtic->isEmpty() && $largosExternos->isEmpty() && !$this->option('force')) {
            $this->info('No se encontraron duplicados ni formatos antiguos. El sistema está íntegro.');
            return;
        }

        $this->warn("Conflictos detectados: " . count($duplicadosEncontrados) . " duplicados entre tablas.");
        $contador = $this->obtenerMaxSN();

        // 3. Resolver duplicados en tabla externa (Prioridad a DTIC)
        $actualizados = 0;
        foreach ($snExternos as $id => $numero) {
            if (in_array($numero, $duplicadosEncontrados)) {
                $nuevoSN = $this->siguienteSN($contador);
                $bien = BienExterno::find($id);
                $this->info("Reasignando duplicado: {$numero} -> {$nuevoSN} (ID: {$id})");
                $bien->update(['numero_bien' => $nuevoSN]);
                $this->actualizarOperaciones($numero, $nuevoSN);
                $actualizados++;
            }
        }

        // 4. Resolver formatos largos
        foreach ($largosDtic as $bien) {
            $antiguo = $bien->numero_bien;
            $nuevo = $this->siguienteSN($contador);
            $bien->update(['numero_bien' => $nuevo]);
            $this->actualizarOperaciones($antiguo, $nuevo);
            $actualizados++;
        }

        foreach ($largosExternos as $bienExt) {
            $antiguo = $bienExt->numero_bien;
            $nuevo = $this->siguienteSN($contador);
            $bienExt->update(['numero_bien' => $nuevo]);
            $this->actualizarOperaciones($antiguo, $nuevo);
            $actualizados++;
        }

        $this->info("¡Proceso completado! Se han corregido {$actualizados} registros.");
    }

    private function actualizarOperaciones(string $antiguoSN, string $nuevoSN): void
    {
        TransferenciaInterna::where('numero_bien', $antiguoSN)->update(['numero_bien' => $nuevoSN]);
        Desincorporacion::where('numero_bien', $antiguoSN)->update(['numero_bien' => $nuevoSN]);
        DistribucionDireccion::where('numero_bien', $antiguoSN)->update(['numero_bien' => $nuevoSN]);
        MovimientoBien::where('numero_bien', $antiguoSN)->update(['numero_bien' => $nuevoSN]);
    }
}
