<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\ResolutionType;
use App\Models\WorkTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkTask>
 */
class WorkTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-15 days', '-5 days');
        $end = (clone $start)->modify('+'.rand(1, 4).' hours');

        return [
            'call_id' => Call::factory(),
            'resolution_type_id' => ResolutionType::factory(),
            'work_started_at' => fake()->dateTimeBetween('-15 days', '-5 days'),
            'work_completed_at' => fake()->dateTimeBetween('-5 days', 'now'),
        ];
    }

    public function withoutResolution(): static
    {
        return $this->state(fn () => [
            'resolution_type_id' => null,
        ]);
    }
}
