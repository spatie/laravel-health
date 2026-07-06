<?php

namespace Spatie\Health\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Health\Enums\Status;
use Spatie\Health\Models\HealthCheckResultHistoryItem;

/**
 * @extends Factory<HealthCheckResultHistoryItem>
 */
class HealthCheckResultHistoryItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'check_name' => fake()->word(),
            'check_label' => fake()->word(),
            'status' => fake()->randomElement(Status::cases()),
            'notification_message' => fake()->text(),
            'short_summary' => fake()->sentences(asText: true),
            'meta' => [],
            'batch' => (string) Str::uuid(),
            'ended_at' => now(),
        ];
    }
}
