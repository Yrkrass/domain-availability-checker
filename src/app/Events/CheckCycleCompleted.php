<?php

namespace App\Events;

use App\Models\CheckSetting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class CheckCycleCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CheckSetting $setting
    ) {}
}
