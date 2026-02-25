<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'BUENO', 'color' => '#34d399'],
            ['nombre' => 'MALO', 'color' => '#fb7185'],
            ['nombre' => 'REGULAR', 'color' => '#fbbf24'],
            ['nombre' => 'EN REPARACION', 'color' => '#60a5fa'],
            ['nombre' => 'DESINCORPORADO', 'color' => '#9ca3af'],
        ];

        foreach ($estados as $estado) {
            \App\Models\Estado::updateOrCreate(
                ['nombre' => $estado['nombre']],
                ['color' => $estado['color']]
            );
        }
    }
}
