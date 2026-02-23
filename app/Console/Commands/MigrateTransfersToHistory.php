<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Bien;
use App\Models\BienExterno;
use App\Models\MovimientoBien;
use App\Models\TransferenciaInterna;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateTransfersToHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-transfers-history {--force : Sobrescribir movimientos existentes vinculados a estas transferencias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra las transferencias existentes de la tabla transferencias_internas al historial centralizado movimientos_bienes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando migración de transferencias al historial...');

        $transferencias = TransferenciaInterna::all();
        $total = $transferencias->count();
        $migrados = 0;
        $omitidos = 0;

        $this->withProgressBar($transferencias, function ($transferencia) use (&$migrados, &$omitidos) {
            // Verificar si ya existe un movimiento para esta transferencia para evitar duplicados
            $existe = MovimientoBien::where('operacion_type', TransferenciaInterna::class)
                ->where('operacion_id', $transferencia->id)
                ->exists();

            if ($existe && !$this->option('force')) {
                $omitidos++;
                return;
            }

            if ($existe && $this->option('force')) {
                MovimientoBien::where('operacion_type', TransferenciaInterna::class)
                    ->where('operacion_id', $transferencia->id)
                    ->delete();
            }

            // Determinar el tipo de bien y su ID actual
            // Nota: Si el bien fue eliminado y recreado en otra tabla, el ID en la transferencia 
            // podría no existir ya. Sin embargo, para el historial usamos la información que hay.
            $bienType = null;
            $bienId = null;

            if ($transferencia->bien_id) {
                $bienType = Bien::class;
                $bienId = $transferencia->bien_id;
            } elseif ($transferencia->bien_externo_id) {
                $bienType = BienExterno::class;
                $bienId = $transferencia->bien_externo_id;
            } else {
                // Si no hay ID, no podemos vincularlo polimórficamente de forma útil, 
                // pero lo registramos con el numero_bien para trazabilidad
                $bienType = Bien::class; // Fallback
                $bienId = 0;
            }

            MovimientoBien::create([
                'bien_type' => $bienType,
                'bien_id' => $bienId,
                'numero_bien' => $transferencia->numero_bien,
                'tipo_movimiento' => MovimientoBien::TIPO_TRANSFERENCIA,
                'operacion_type' => TransferenciaInterna::class,
                'operacion_id' => $transferencia->id,
                'departamento_origen_id' => $transferencia->procedencia_id,
                'departamento_destino_id' => $transferencia->destino_id,
                'area_origen_id' => $transferencia->area_procedencia_id,
                'area_destino_id' => $transferencia->area_id,
                'descripcion' => $transferencia->descripcion ?: 'Migración de transferencia histórica',
                'fecha' => $transferencia->fecha ?: ($transferencia->created_at ? $transferencia->created_at->toDateString() : now()->toDateString()),
                'user_id' => $transferencia->user_id,
            ]);

            $migrados++;
        });

        $this->newLine(2);
        $this->info("Proceso completado.");
        $this->info("- Total procesados: {$total}");
        $this->info("- Migrados exitosamente: {$migrados}");
        $this->info("- Omitidos (ya existían): {$omitidos}");

        return Command::SUCCESS;
    }
}
