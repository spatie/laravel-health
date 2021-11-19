<?php

namespace Spatie\Health\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckResultHistoryItem extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'meta' => 'array',
        'started_failing_at' => 'timestamp',
    ];
}
