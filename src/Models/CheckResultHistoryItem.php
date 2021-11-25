<?php

namespace Spatie\Health\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $created_at
 * @property string $batch
 * @property string $ended_at
 * @property string $message
 * @property array<string, mixed> $meta
 * @property string $status
 * @property string $check_name
 */
class CheckResultHistoryItem extends Model
{
    use HasFactory;
    use MassPrunable;

    public $guarded = [];

    /** @var array<string,string> */
    public $casts = [
        'meta' => 'array',
        'started_failing_at' => 'timestamp',
    ];

    public function prunable(): void
    {
        $days = config('health.keep_history_for_days');

        static::where('created_at', '<=', now()->subDays($days));
    }
}
