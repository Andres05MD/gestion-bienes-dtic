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
            'Jefatura',
            'Asistente',
            'Programación',
            'Soporte Técnico',
            'Carnetización',
            'Secretaría',
            'Cuarto de Servidores',
            'Central Telefónica',
            'Área Común',
        ];

        foreach ($areas as $area) {
            \App\Models\Area::updateOrCreate(
                ['nombre' => $area]
            );
        }
    }
}
