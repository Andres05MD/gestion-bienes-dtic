<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bien>
 */
class BienFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero_bien' => $this->faker->unique()->numerify('#####'),
            'observaciones' => $this->faker->sentence(),
            'serial' => $this->faker->unique()->bothify('??###??'),
            'area_id' => \App\Models\Area::factory(),
            'categoria_bien_id' => \App\Models\CategoriaBien::factory(),
            'estado_id' => \App\Models\Estado::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
