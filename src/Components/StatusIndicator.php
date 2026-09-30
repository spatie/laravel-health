<?php

namespace Spatie\Health\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;

class StatusIndicator extends Component
{
    public function __construct(public StoredCheckResult $result) {}

    public function render(): View
    {
        return view('health::status-indicator', [
            'result' => $this->result,
        ]);
    }
}
