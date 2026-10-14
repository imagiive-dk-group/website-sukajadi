<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class Reports extends Page
{
    /** Status yang dihitung sebagai pendapatan (perkiraan). */
    public const REVENUE_STATUSES = ['confirmed', 'completed'];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|UnitEnum|null $navigationGroup = 'Booking';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Laporan Booking';

    protected static ?string $slug = 'laporan';

    protected string $view = 'filament.pages.reports';

    #[Url(as: 'tahun')]
    public int|string $year = '';

    /** Periode ekspor: 'all' = setahun penuh, '1'..'12' = bulan tertentu. */
    #[Url(as: 'bulan')]
    public string $period = 'all';

    public function mount(): void
    {
        $this->year = $this->selectedYear();
    }

    public function selectedYear(): int
    {
        $year = (int) $this->year;

        return ($year >= 2000 && $year <= 2100) ? $year : (int) now()->year;
    }

    public function selectedMonth(): ?int
    {
        $month = (int) $this->period;

        return ($month >= 1 && $month <= 12) ? $month : null;
    }

    /** @return array<int, int> */
    public function yearOptions(): array
    {
        $first = Booking::query()->min('visit_date');
        $last = Booking::query()->max('visit_date');
        $from = min($first ? Carbon::parse($first)->year : now()->year, now()->year);
        $to = max($last ? Carbon::parse($last)->year : now()->year, now()->year + 1);

        return range($to, $from);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor CSV (Excel)')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(fn (): StreamedResponse => $this->exportCsv()),
        ];
    }

    /** Booking pada tahun (dan bulan, bila dipilih) yang sedang dilihat. */
    protected function bookingsForPeriod(?int $month = null): Collection
    {
        $year = $this->selectedYear();
        $start = Carbon::create($year, $month ?? 1, 1)->startOfDay();
        $end = $month ? $start->copy()->endOfMonth() : $start->copy()->endOfYear();

        return Booking::query()
            ->with('package')
            ->whereDate('visit_date', '>=', $start)
            ->whereDate('visit_date', '<=', $end)
            ->orderBy('visit_date')
            ->orderBy('code')
            ->get();
    }

    protected function packageLabel(Booking $booking): string
    {
        if ($booking->type === 'custom') {
            return 'Paket Custom';
        }

        return $booking->package ? (string) localized($booking->package->name, 'id') : $booking->package_name;
    }

    /**
     * @return array{bookings: int, pax: int, revenue: int, cancelled: int}
     */
    protected function summarize(Collection $bookings): array
    {
        $active = $bookings->where('status', '!=', 'cancelled');

        return [
            'bookings' => $active->count(),
            'pax' => (int) $active->sum('pax'),
            'revenue' => (int) $bookings->whereIn('status', self::REVENUE_STATUSES)->sum('total'),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
        ];
    }

    protected function getViewData(): array
    {
        $bookings = $this->bookingsForPeriod();

        $months = collect(range(1, 12))->map(function (int $month) use ($bookings): array {
            $items = $bookings->filter(fn (Booking $b): bool => $b->visit_date->month === $month);

            return [
                'label' => Carbon::create(2000, $month, 1)->translatedFormat('F'),
                ...$this->summarize($items),
                'packages' => $items->where('status', '!=', 'cancelled')
                    ->groupBy(fn (Booking $b): string => $this->packageLabel($b))
                    ->map(fn (Collection $group, string $name): string => $name.': '.$group->count().' ('.$group->sum('pax').' pax)')
                    ->values()
                    ->all(),
            ];
        });

        $packages = $bookings
            ->groupBy(fn (Booking $b): string => $this->packageLabel($b))
            ->map(fn (Collection $group, string $name): array => ['name' => $name, ...$this->summarize($group)])
            ->sortByDesc('pax')
            ->values();

        return [
            'months' => $months,
            'packages' => $packages,
            'total' => $this->summarize($bookings),
            'years' => $this->yearOptions(),
            'monthOptions' => collect(range(1, 12))
                ->mapWithKeys(fn (int $m): array => [$m => Carbon::create(2000, $m, 1)->translatedFormat('F')])
                ->all(),
        ];
    }

    public function exportCsv(): StreamedResponse
    {
        $month = $this->selectedMonth();
        $bookings = $this->bookingsForPeriod($month);
        $filename = 'booking-sukajadi-'.$this->selectedYear().($month ? '-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT) : '').'.csv';

        $headers = [
            'Kode', 'Tanggal kunjungan', 'Paket', 'Jenis', 'Pax', 'Harga per pax', 'Subtotal', 'Tambahan',
            'Total tambahan', 'Estimasi total', 'Status', 'Nama pemesan', 'WhatsApp', 'Email', 'Asal / instansi',
            'Jenis rombongan', 'Bahasa', 'Catatan pemesan', 'Catatan admin', 'Masuk', 'Dikonfirmasi',
        ];

        return response()->streamDownload(function () use ($bookings, $headers): void {
            $out = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers, ';', '"', '');

            foreach ($bookings as $booking) {
                $addons = collect($booking->addons ?? [])
                    ->map(fn (array $a): string => localized($a['name'] ?? null, 'id').' ('.($a['subtotal'] ?? 0).')')
                    ->implode(', ');

                fputcsv($out, [
                    $booking->code,
                    $booking->visit_date->format('Y-m-d'),
                    $booking->package_name,
                    $booking->type === 'custom' ? 'Custom' : 'Paket',
                    $booking->pax,
                    $booking->price_per_pax,
                    $booking->subtotal,
                    $addons,
                    $booking->addons_total,
                    $booking->total,
                    __('site.status.'.$booking->status),
                    $booking->customer_name,
                    $booking->phone,
                    $booking->email,
                    $booking->organization,
                    __('site.group_types.'.$booking->group_type),
                    $booking->locale,
                    $booking->notes,
                    $booking->admin_notes,
                    $booking->created_at?->format('Y-m-d H:i'),
                    $booking->confirmed_at?->format('Y-m-d H:i'),
                ], ';', '"', '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
