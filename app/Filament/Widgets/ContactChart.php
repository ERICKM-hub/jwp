<?php

namespace App\Filament\Widgets;

use App\Models\ContactInquiry;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ContactChart extends ChartWidget
{
    protected ?string $heading = 'Contact Chart';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $months = 12;
        $startDate = now()->subMonths($months - 1)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Use strftime() for SQLite compatibility
        $counts = ContactInquiry::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('COUNT(*) as aggregate')
            )
            ->groupBy('month')
            ->pluck('aggregate', 'month');

        $labels = [];
        $data = [];

        foreach (CarbonPeriod::create($startDate, '1 month', $endDate) as $date) {
            $monthKey = $date->format('Y-m');

            $labels[] = $date->format('M Y');
            $data[] = $counts->get($monthKey, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 10,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
