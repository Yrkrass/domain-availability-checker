<?php

namespace App\Console\Commands;

use App\Enums\CheckMode;
use App\Models\CheckSetting;
use App\Jobs\CheckDomainsJob;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class RunScheduledChecks extends Command
{
    protected $signature = 'checks:run-scheduled';

    protected $description = 'Run scheduled domain checks';

    public function handle(): void
    {
        CheckSetting::where('is_active', true)
            ->where('mode', CheckMode::Auto)
            ->where('is_running', false)
            ->where('starts_at', '<=', now())
            ->withMax('checkLogs', 'created_at')
            ->get()
            ->each(function (CheckSetting $setting) {
                $lastCheckedAt = $setting->check_logs_max_created_at;

                if (! $lastCheckedAt || Carbon::parse($lastCheckedAt)->addMinutes($setting->interval)->isPast()) {
                    $this->info("Dispatching checks for setting #{$setting->id}");
                    CheckDomainsJob::dispatch($setting);
                }
            });
    }
}
