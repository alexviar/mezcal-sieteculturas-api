<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'presentation' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => $this->faker->sentence,
            'variant' => $this->faker->sentence,
            'price' => $this->faker->numerify('###.##'),
            'discount' => $this->faker->numerify('###.##'),
            'shipping_value' => $this->faker->numerify('###.##'),
            'stock' => $this->faker->numerify('###'),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}
