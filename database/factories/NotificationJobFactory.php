<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationJob>
 */
class NotificationJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'minute' => '*',
            'hour' => '*',
            'day' => '*',
            'month' => '*',
            'weekday' => '*',
            'timezone' => fake()->optional()->timezone(),
            'event' => fake()->word(),
            'title' => Str::ucfirst(fake()->optional()->word()),
            'content' => Arr::map(
                fake()->sentences(random_int(1, 3)), function ($value) {
                    return ['content' => $value];
                }
            ),
            'is_active' => fake()->boolean(),
        ];
    }
}
