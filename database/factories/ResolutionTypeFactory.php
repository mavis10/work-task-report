<?php

namespace Database\Factories;

use App\Models\ResolutionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResolutionType>
 */
class ResolutionTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'name' => fake()->randomElement([
                'Fix Complete – Parts Collection Required',
                'Further Diagnosis – Internal – 3rd Party Repair',
                'Awaiting Purchase Order from Customer',
                'Call on Hold at Customer Request',
                'Fix Complete – Collection Arranged',
            ]),
            'description' => fake()->sentence(),
        ];
    }
}
