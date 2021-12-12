<?php

namespace Spatie\Health\Components;

use Illuminate\View\View;
use Illuminate\View\Component;

class Logo extends Component
{
    public function render(): View
    {
        return view('health::logo');
    }
}
