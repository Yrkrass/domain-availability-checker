<?php

namespace App\Jobs;

use App\Models\CheckSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckDomainsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CheckSetting $setting
    ) {}

    public function handle(): void
    {
        Log::info('CheckDomainsJob started', ['setting_id' => $this->setting->id]);

        $jobs = $this->setting->user->domains
            ->map(fn ($domain) => new CheckDomainJob($domain, $this->setting))
            ->toArray();

        $count = count($jobs);

        Log::info('Jobs count', ['count' => $count]);

        $this->setting->update([
            'is_running' => true,
            'domains_count' => $count,
            'checked_count' => 0,
        ]);

        foreach ($jobs as $job) {
            dispatch($job);
        }

        Log::info('Jobs dispatched');
    }
}
