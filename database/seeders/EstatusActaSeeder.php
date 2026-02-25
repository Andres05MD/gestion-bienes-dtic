<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstatusActaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estatus = [
            ['nombre' => 'ACTAS LISTAS', 'color' => '#22c55e'], // Verde
            ['nombre' => 'ACTA FIRMADA FALTA COPIA', 'color' => '#f59e0b'], // Ámbar/Naranja
            ['nombre' => 'PENDIENTE', 'color' => '#ef4444'], // Rojo/Pendiente
        ];

        foreach ($estatus as $item) {
            \App\Models\EstatusActa::firstOrCreate(
                ['nombre' => $item['nombre']],
                ['color' => $item['color']]
            );
        }
    }
}
