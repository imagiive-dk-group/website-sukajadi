<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BookingsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '260px';

    public function getHeading(): string
    {
        return 'Booking per bulan ('.now()->year.')';
    }

    public function getDescription(): ?string
    {
        return 'Berdasarkan tanggal kunjungan, tanpa booking yang dibatalkan.';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $bookings = Booking::query()
            ->holdingCapacity()
            ->whereDate('visit_date', '>=', now()->startOfYear())
            ->whereDate('visit_date', '<=', now()->endOfYear())
            ->get(['visit_date', 'pax']);

        $counts = [];
        $pax = [];
        foreach (range(1, 12) as $month) {
            $items = $bookings->filter(fn (Booking $b): bool => $b->visit_date->month === $month);
            $counts[] = $items->count();
            $pax[] = (int) $items->sum('pax');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Booking',
                    'data' => $counts,
                    'backgroundColor' => '#277721',
                    'borderColor' => '#277721',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Peserta (pax)',
                    'data' => $pax,
                    'type' => 'line',
                    'backgroundColor' => '#FA6700',
                    'borderColor' => '#FA6700',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => collect(range(1, 12))->map(fn (int $m): string => Carbon::create(2000, $m, 1)->translatedFormat('M'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'position' => 'left', 'ticks' => ['precision' => 0]],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false], 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
