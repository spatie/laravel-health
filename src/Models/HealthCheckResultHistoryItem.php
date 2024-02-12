<?php

namespace Spatie\Health\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Health\ResultStores\EloquentHealthResultStore;

/**
 * @property \Carbon\Carbon $created_at
 * @property string $batch
 * @property string $ended_at
 * @property string $notification_message
 * @property string $short_summary
 * @property array<string, mixed> $meta
 * @property string $status
 * @property string $check_name
 * @property string $check_label
 */
class HealthCheckResultHistoryItem extends Model
{
    use HasFactory;
    use MassPrunable;

    protected $guarded = [];

    /** @var array<string,string> */
    public $casts = [
        'meta' => 'array',
        'started_failing_at' => 'timestamp',
    ];

    public function getConnectionName(): string
    {
        return $this->connection ?:
            config('health.result_stores.'.EloquentHealthResultStore::class.'.connection') ?:
            config('database.default');
    }

    public function prunable(): Builder
    {
        $days = config('health.result_stores.'.EloquentHealthResultStore::class.'.keep_history_for_days') ?? 5;

        return static::where('created_at', '<=', now()->subDays($days));
    }
}
