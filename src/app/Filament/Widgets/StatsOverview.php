<?php

namespace App\Filament\Widgets;

use App\Models\Domain;
use App\Models\CheckLog;
use App\Models\CheckSetting;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalDomains = Domain::where('user_id', $userId)->count();
        $availableDomains = Domain::where('user_id', $userId)->where('is_available', true)->count();
        $unavailableDomains = Domain::where('user_id', $userId)->where('is_available', false)->count();
        $activeSettings = CheckSetting::where('user_id', $userId)->where('is_active', true)->count();
        $totalChecks = CheckLog::whereHas('domain', fn ($q) => $q->where('user_id', $userId))->count();
        $avgResponseTime = CheckLog::whereHas('domain', fn ($q) => $q->where('user_id', $userId))
            ->whereNotNull('response_time')
            ->avg('response_time');

        return [
            Stat::make('Total Domains', $totalDomains)
                ->icon('heroicon-o-globe-alt')
                ->color('primary'),
            Stat::make('Available', $availableDomains)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Unavailable', $unavailableDomains)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('Active Settings', $activeSettings)
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info'),
            Stat::make('Total Checks', $totalChecks)
                ->icon('heroicon-o-arrow-path')
                ->color('warning'),
            Stat::make('Avg Response Time', $avgResponseTime === null
                ? 'No data'
                : ($avgResponseTime >= 1000
                    ? number_format($avgResponseTime / 1000, 2).' sec'
                    : round($avgResponseTime).' ms'
                ))
                ->icon('heroicon-o-clock')
                ->color('gray'),
        ];
    }
}
