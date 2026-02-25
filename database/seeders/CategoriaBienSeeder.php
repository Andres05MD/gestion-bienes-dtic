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
            'BIEN NACIONAL',
            'BIEN ESTADAL',
            'BIEN DE TERCEROS',
            'COMODATO',
            'BIEN MENOR',
            'PENDIENTE POR CATEGORIA',
        ];

        foreach ($categorias as $categoria) {
            \App\Models\CategoriaBien::updateOrCreate([
                'nombre' => $categoria,
            ]);
        }
    }
}
