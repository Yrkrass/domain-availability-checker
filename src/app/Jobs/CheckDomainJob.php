<?php

namespace App\Jobs;

use Exception;
use App\Models\Domain;
use App\Models\CheckLog;
use App\Enums\CheckMethod;
use App\Models\CheckSetting;
use Illuminate\Support\Facades\DB;
use App\Events\CheckCycleCompleted;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckDomainJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Domain $domain,
        public CheckSetting $setting
    ) {}

    public function handle(): void
    {
        $this->setting->refresh();

        if (! $this->setting->is_running) {
            return;
        }

        $start = microtime(true);

        try {
            $method = $this->setting->method;

            $response = match ($method) {
                CheckMethod::Head => Http::timeout($this->setting->timeout)->head($this->domain->url),
                CheckMethod::Get => Http::timeout($this->setting->timeout)->get($this->domain->url),
                CheckMethod::Both => $this->headWithFallback(),
            };

            $responseTime = round((microtime(true) - $start) * 1000);
            $code = $response->status();
            $isAvailable = $response->successful();

            CheckLog::create([
                'domain_id' => $this->domain->id,
                'check_setting_id' => $this->setting->id,
                'is_available' => $isAvailable,
                'response_code' => $code,
                'response_time' => $responseTime,
                'error' => $isAvailable ? null : "HTTP {$code}",
            ]);

            $this->domain->update([
                'is_available' => $isAvailable,
                'checked_at' => now(),
            ]);

        } catch (Exception $e) {
            CheckLog::create([
                'domain_id' => $this->domain->id,
                'check_setting_id' => $this->setting->id,
                'is_available' => false,
                'response_code' => null,
                'response_time' => round((microtime(true) - $start) * 1000),
                'error' => $e->getMessage(),
            ]);

            $this->domain->update([
                'is_available' => false,
                'checked_at' => now(),
            ]);

        } finally {
            DB::transaction(function () {
                $this->setting->increment('checked_count');

                $setting = CheckSetting::where('id', $this->setting->id)
                    ->lockForUpdate()
                    ->first();

                Log::info('Check', [
                    'checked' => $setting->checked_count,
                    'total' => $setting->domains_count,
                    'is_running' => $setting->is_running,
                ]);

                if ($setting->checked_count >= $setting->domains_count && $setting->is_running) {
                    $setting->update(['is_running' => false]);

                    CheckCycleCompleted::dispatch($setting);
                }
            });
        }
    }

    private function headWithFallback(): Response
    {
        $request = Http::timeout($this->setting->timeout);

        $response = $request->head($this->domain->url);

        if ($response->status() === 405) {
            return $request->get($this->domain->url);
        }

        return $response;
    }
}
