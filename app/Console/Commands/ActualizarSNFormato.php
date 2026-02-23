<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bien;
use App\Models\BienExterno;

class ActualizarSNFormato extends Command
{
    protected $signature = 'bienes:actualizar-sn';

    protected $description = 'Convierte los códigos S/N antiguos a S/N secuenciales. Usa una secuencia GLOBAL entre bienes DTIC y externos para evitar duplicados.';

    /**
     * Obtiene el máximo número S/N actual entre ambas tablas.
     */
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

    /**
     * Genera el siguiente S/N secuencial a partir de un contador.
     */
    private function siguienteSN(int &$contador): string
    {
        $contador++;
        return 'S/N-' . str_pad((string) $contador, 3, '0', STR_PAD_LEFT);
    }

    public function handle()
    {
        $this->info('Iniciando la actualización de formato S/N (secuencia global)...');
        $this->newLine();

        // Paso 1: Identificar bienes con formato S/N antiguo (largo, no secuencial)
        $bienesDtic = Bien::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8')
            ->get();

        $bienesExternos = BienExterno::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8')
            ->get();

        if ($bienesDtic->isEmpty() && $bienesExternos->isEmpty()) {
            $this->info('No se encontraron bienes con formato S/N antiguo. Todo está actualizado.');
            return;
        }

        $this->info("Encontrados: {$bienesDtic->count()} bienes DTIC + {$bienesExternos->count()} bienes externos con S/N antiguo.");

        // Paso 2: Obtener el máximo S/N válido actual entre AMBAS tablas
        $contador = $this->obtenerMaxSN();
        $this->info("Último S/N válido encontrado: S/N-" . str_pad((string) $contador, 3, '0', STR_PAD_LEFT));

        // Paso 3: Asignar nuevos S/N a bienes DTIC primero
        $contadorBienes = 0;
        foreach ($bienesDtic as $bien) {
            $antiguoSN = $bien->numero_bien;
            $nuevoSN = $this->siguienteSN($contador);
            $bien->update(['numero_bien' => $nuevoSN]);

            // Actualizar operaciones que referencian este bien por numero_bien antiguo
            $this->actualizarOperaciones($antiguoSN, $nuevoSN);
            $contadorBienes++;
        }

        // Paso 4: Continuar la secuencia para bienes externos
        $contadorExternos = 0;
        foreach ($bienesExternos as $bienExt) {
            $antiguoSN = $bienExt->numero_bien;
            $nuevoSN = $this->siguienteSN($contador);
            $bienExt->update(['numero_bien' => $nuevoSN]);

            // Actualizar operaciones que referencian este bien por numero_bien antiguo
            $this->actualizarOperaciones($antiguoSN, $nuevoSN);
            $contadorExternos++;
        }

        // Paso 5: Actualizar operaciones huérfanas restantes (que no matchearon con ningún bien)
        $contadorHuerfanas = 0;

        $transferencias = \App\Models\TransferenciaInterna::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8')
            ->get();
        foreach ($transferencias as $op) {
            $op->update(['numero_bien' => $this->siguienteSN($contador)]);
            $contadorHuerfanas++;
        }

        $desincorporaciones = \App\Models\Desincorporacion::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8')
            ->get();
        foreach ($desincorporaciones as $op) {
            $op->update(['numero_bien' => $this->siguienteSN($contador)]);
            $contadorHuerfanas++;
        }

        $distribuciones = \App\Models\DistribucionDireccion::where('numero_bien', 'LIKE', 'S/N-%')
            ->whereRaw('LENGTH(numero_bien) > 8')
            ->get();
        foreach ($distribuciones as $op) {
            $op->update(['numero_bien' => $this->siguienteSN($contador)]);
            $contadorHuerfanas++;
        }

        $this->newLine();
        $this->info("Proceso finalizado.");
        $this->info("- Bienes DTIC actualizados: {$contadorBienes}");
        $this->info("- Bienes Externos actualizados: {$contadorExternos}");
        $this->info("- Operaciones huérfanas actualizadas: {$contadorHuerfanas}");
        $this->info("- Último S/N asignado: S/N-" . str_pad((string) $contador, 3, '0', STR_PAD_LEFT));
    }

    /**
     * Actualiza el numero_bien en todas las tablas de operaciones
     * que referencian un S/N antiguo.
     */
    private function actualizarOperaciones(string $antiguoSN, string $nuevoSN): void
    {
        \App\Models\TransferenciaInterna::where('numero_bien', $antiguoSN)
            ->update(['numero_bien' => $nuevoSN]);

        \App\Models\Desincorporacion::where('numero_bien', $antiguoSN)
            ->update(['numero_bien' => $nuevoSN]);

        \App\Models\DistribucionDireccion::where('numero_bien', $antiguoSN)
            ->update(['numero_bien' => $nuevoSN]);

        // También actualizar en historial de movimientos
        \App\Models\MovimientoBien::where('numero_bien', $antiguoSN)
            ->update(['numero_bien' => $nuevoSN]);
    }
}
