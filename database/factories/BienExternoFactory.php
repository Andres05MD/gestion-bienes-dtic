<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BienExterno>
 */
class BienExternoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero_bien' => $this->faker->unique()->numerify('#####'),
            'descripcion' => $this->faker->sentence(),
            'serial' => $this->faker->unique()->bothify('??###??'),
            'departamento_id' => \App\Models\Departamento::factory(),
            'categoria_bien_id' => \App\Models\CategoriaBien::factory(),
            // 'estado_id' => \App\Models\Estado::factory(), // Assuming it has estado_id based on previous file reads
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
