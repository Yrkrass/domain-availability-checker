<?php

namespace App\Filament\Widgets;

use App\Models\CheckLog;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class AvailabilityRateChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Availability Rate - Last 30 Days';

    protected function getData(): array
    {
        $userId = auth()->id();
        $from = Carbon::today()->subDays(29);

        $logs = CheckLog::whereHas('domain', fn ($q) => $q->where('user_id', $userId))
            ->whereDate('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN is_available THEN 1 ELSE 0 END) as available')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $data = collect(range(29, 0))->map(function ($daysAgo) use ($logs) {
            $date = Carbon::today()->subDays($daysAgo);
            $row = $logs->get($date->format('Y-m-d'));
            $total = (int) ($row->total ?? 0);
            $available = (int) ($row->available ?? 0);

            return [
                'date' => $date->format('d.m'),
                'rate' => $total > 0 ? round($available / $total * 100, 1) : 0,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Availability %',
                    'data' => $data->pluck('rate')->toArray(),
                    'borderColor' => '#22c55e',
                    'fill' => false,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
