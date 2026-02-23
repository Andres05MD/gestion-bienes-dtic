<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\MovimientoBien;
use Illuminate\Console\Command;

class CorregirMovimientosBienId extends Command
{
    protected $signature = 'movimientos:corregir-bien-id';
    protected $description = 'Corrige los registros de movimientos que tienen bien_id=0, buscando el bien correcto por numero_bien';

    public function handle(): int
    {
        $movimientos = MovimientoBien::where('bien_id', 0)->get();

        if ($movimientos->isEmpty()) {
            $this->info('✅ No hay movimientos con bien_id=0. Todo está correcto.');
            return self::SUCCESS;
        }

        $this->info("Se encontraron {$movimientos->count()} movimiento(s) con bien_id=0.");
        $this->newLine();

        $corregidos = 0;
        $noEncontrados = 0;

        foreach ($movimientos as $mov) {
            // Buscar primero en la tabla de bienes DTIC
            $bien = Bien::where('numero_bien', $mov->numero_bien)->first();

            if ($bien) {
                $mov->update([
                    'bien_type' => Bien::class,
                    'bien_id' => $bien->id,
                ]);
                $this->line("  <fg=green>✓</> Mov #{$mov->id} (SN: {$mov->numero_bien}) → Bien DTIC ID={$bien->id}");
                $corregidos++;
                continue;
            }

            // Si no se encuentra en DTIC, buscar en bienes externos
            $bienExterno = BienExterno::where('numero_bien', $mov->numero_bien)->first();

            if ($bienExterno) {
                $mov->update([
                    'bien_type' => BienExterno::class,
                    'bien_id' => $bienExterno->id,
                ]);
                $this->line("  <fg=green>✓</> Mov #{$mov->id} (SN: {$mov->numero_bien}) → BienExterno ID={$bienExterno->id}");
                $corregidos++;
                continue;
            }

            // No se encontró el bien en ninguna tabla
            $this->line("  <fg=red>✗</> Mov #{$mov->id} (SN: {$mov->numero_bien}) → No se encontró el bien en ninguna tabla");
            $noEncontrados++;
        }

        $this->newLine();
        $this->info("Resultado: {$corregidos} corregido(s), {$noEncontrados} sin encontrar.");

        return self::SUCCESS;
    }
}
