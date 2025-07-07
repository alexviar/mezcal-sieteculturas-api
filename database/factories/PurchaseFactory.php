<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date,
            'value' => $this->faker->numerify('###.##'),
            'quantity' => $this->faker->numerify('###'),
            'payment_type' => $this->faker->randomElement(['efectivo', 'stripe']),
            'customer_name' => $this->faker->name,
            'customer_mail' => $this->faker->email,
            'customer_city' => $this->faker->city,
            'customer_phone' => $this->faker->phoneNumber,
            'product_id' => Product::factory()
        ];
    }

    public function pending(){
        return $this->state([
            'shipped' => false
        ]);
    }

    public function shipped(){
        return $this->state([
            'shipped' => true
        ]);
    }
}
