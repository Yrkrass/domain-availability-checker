<?php

namespace App\Filament\Widgets;

use App\Models\CheckLog;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class DomainAvailabilityChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Domain Availability - Last 7 Days';

    protected function getData(): array
    {
        $userId = auth()->id();
        $from = Carbon::today()->subDays(6);

        $logs = CheckLog::whereHas('domain', fn ($q) => $q->where('user_id', $userId))
            ->whereDate('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN is_available THEN 1 ELSE 0 END) as available')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $data = collect(range(6, 0))->map(function ($daysAgo) use ($logs) {
            $date = Carbon::today()->subDays($daysAgo);
            $row = $logs->get($date->format('Y-m-d'));
            $total = (int) ($row->total ?? 0);
            $available = (int) ($row->available ?? 0);

            return [
                'date' => $date->format('d.m'),
                'available' => $available,
                'unavailable' => $total - $available,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Available',
                    'data' => $data->pluck('available')->toArray(),
                    'backgroundColor' => '#22c55e',
                ],
                [
                    'label' => 'Unavailable',
                    'data' => $data->pluck('unavailable')->toArray(),
                    'backgroundColor' => '#ef4444',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
