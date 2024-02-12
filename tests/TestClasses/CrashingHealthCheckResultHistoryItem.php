<?php

namespace Spatie\Health\Tests\TestClasses;

use Spatie\Health\Models\HealthCheckResultHistoryItem;

class CrashingHealthCheckResultHistoryItem extends HealthCheckResultHistoryItem
{
    protected $connection = 'custom';
}
