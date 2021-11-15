<?php

namespace Spatie\Health\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckResult extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'started_failing_at' => 'timestamp',
    ];
}
