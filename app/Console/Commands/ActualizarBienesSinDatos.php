<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ActualizarBienesSinDatos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:actualizar-bienes-sin-datos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza bienes registrados sin número (S/N) y sin categoría (PENDIENTE POR CATEGORIA)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando estandarización de bienes...');

        $categoria = \App\Models\CategoriaBien::where('nombre', 'PENDIENTE POR CATEGORIA')->first();

        if (!$categoria) {
            $this->error('No se encontró la categoría "PENDIENTE POR CATEGORIA".');
            return 1;
        }

        $categoriaId = $categoria->id;

        // Actualizar Bienes DTIC
        $this->info('Actualizando Bienes DTIC...');
        
        // Número de bien - Iterar para generar IDs únicos y evitar restricción UNIQUE
        $bienesSinNumeroCount = 0;
        \App\Models\Bien::where(function($q) {
            $q->whereNull('numero_bien')->orWhere('numero_bien', '');
        })->chunkById(100, function($bienes) use (&$bienesSinNumeroCount) {
            foreach ($bienes as $bien) {
                $bien->update(['numero_bien' => 'S/N-' . strtoupper(uniqid())]);
                $bienesSinNumeroCount++;
            }
        });

        // Categoría - Esto sí puede ser masivo
        $bienesSinCategoria = \App\Models\Bien::whereNull('categoria_bien_id')
            ->update(['categoria_bien_id' => $categoriaId]);

        $this->line("Bienes DTIC: {$bienesSinNumeroCount} números actualizados, {$bienesSinCategoria} categorías actualizadas.");

        // Actualizar Bienes Externos
        $this->info('Actualizando Bienes Externos...');

        // Número de bien - Iterar para generar IDs únicos
        $externosSinNumeroCount = 0;
        \App\Models\BienExterno::where(function($q) {
            $q->whereNull('numero_bien')->orWhere('numero_bien', '');
        })->chunkById(100, function($externos) use (&$externosSinNumeroCount) {
            foreach ($externos as $externo) {
                $externo->update(['numero_bien' => 'S/N-' . strtoupper(uniqid())]);
                $externosSinNumeroCount++;
            }
        });

        // Categoría
        $externosSinCategoria = \App\Models\BienExterno::whereNull('categoria_bien_id')
            ->update(['categoria_bien_id' => $categoriaId]);

        $this->line("Bienes Externos: {$externosSinNumeroCount} números actualizados, {$externosSinCategoria} categorías actualizadas.");

        $this->info('Estandarización completada con éxito.');
        
        return 0;
    }
}
