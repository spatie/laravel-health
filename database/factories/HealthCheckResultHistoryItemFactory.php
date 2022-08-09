<?php

namespace Spatie\Health\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Health\Enums\Status;
use Spatie\Health\Models\HealthCheckResultHistoryItem;

class HealthCheckResultHistoryItemFactory extends Factory
{
    protected $model = HealthCheckResultHistoryItem::class;

    public function definition(): array
    {
        return [
            'check_name' => $this->faker->word(),
            'check_label' => $this->faker->word(),
            'status' => $this->faker->randomElement(Status::toArray()),
            'notification_message' => $this->faker->text(),
            'short_summary' => $this->faker->sentences(asText: true),
            'meta' => [],
            'batch' => (string) Str::uuid(),
            'ended_at' => now(),
        ];
    }
}
