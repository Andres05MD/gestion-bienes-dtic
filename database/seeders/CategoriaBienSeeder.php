<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaBienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            'Bien Nacional',
            'Bien Estadal',
            'Bien de Terceros', 
            'Comodato',
            'Bien Menor',
            'PENDIENTE POR CATEGORIA',
        ];

        foreach ($categorias as $categoria) {
            \App\Models\CategoriaBien::updateOrCreate([
                'nombre' => $categoria,
            ]);
        }
    }
}
