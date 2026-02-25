<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            'JEFATURA',
            'ASISTENTE',
            'PROGRAMACIÓN',
            'SOPORTE TÉCNICO',
            'CARNETIZACIÓN',
            'SECRETARÍA',
            'CUARTO DE SERVIDORES',
            'CENTRAL TELEFÓNICA',
            'ÁREA COMÚN',
        ];

        foreach ($areas as $area) {
            \App\Models\Area::updateOrCreate(
                ['nombre' => $area]
            );
        }
    }
}
