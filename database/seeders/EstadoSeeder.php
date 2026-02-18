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
            'Bueno',
            'Malo',
            'Regular',
            'En Reparacion',
            'Desincorporado',
        ];

        foreach ($estados as $estado) {
            DB::table('estados')->insert([
                'nombre' => $estado,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
