<?php

namespace App\Filament\Pages;

use App\Filament\Support\Fields;
use App\Models\Attraction;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Partner;
use App\Models\PartnerOffer;
use App\Models\Umkm;
use App\Models\UmkmProduct;
use App\Models\WaClick;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

/**
 * Pemantauan prospek, klik WhatsApp, penjualan per jenis, dan komisi mitra.
 */
class Monitoring extends Page
{
    public const PERIODS = [
        '30d' => '30 hari terakhir',
        'month' => 'Bulan ini',
        'year' => 'Tahun ini',
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static string|UnitEnum|null $navigationGroup = 'Prospek & Monitoring';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Monitoring';

    protected static ?string $title = 'Monitoring Prospek & Penjualan';

    protected static ?string $slug = 'monitoring';

    protected string $view = 'filament.pages.monitoring';

    #[Url(as: 'periode')]
    public string $period = '30d';

    public function mount(): void
    {
        if (! array_key_exists($this->period, self::PERIODS)) {
            $this->period = '30d';
        }
    }

    /** @return array{0: Carbon, 1: Carbon} */
    public function range(): array
    {
        $end = now()->endOfDay();

        $start = match ($this->period) {
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => today()->subDays(29),
        };

        return [$start->copy()->startOfDay(), $end];
    }

    public function periodLabel(): string
    {
        [$start, $end] = $this->range();

        return (self::PERIODS[$this->period] ?? self::PERIODS['30d'])
            .' ('.$start->translatedFormat('j M Y').' – '.$end->translatedFormat('j M Y').')';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCommission')
                ->label('Ekspor komisi mitra (CSV)')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->action(fn (): StreamedResponse => $this->exportCommissionCsv()),
        ];
    }

    /** @return array{statuses: array<int, array{key: string, label: string, count: int, color: string}>, total: int, won: int, conversion: float, sources: array<string, int>} */
    protected function leadStats(): array
    {
        [$start, $end] = $this->range();

        $leads = Lead::query()->whereBetween('created_at', [$start, $end])->get(['status', 'source']);
        $total = $leads->count();
        $won = $leads->where('status', 'won')->count();

        return [
            'statuses' => collect(config('sukajadi.lead_statuses'))->map(fn (string $status): array => [
                'key' => $status,
                'label' => __('site.lead_statuses.'.$status),
                'count' => $leads->where('status', $status)->count(),
                'color' => Fields::leadStatusColor($status),
            ])->all(),
            'total' => $total,
            'won' => $won,
            'conversion' => $total ? round($won / $total * 100, 1) : 0.0,
            'sources' => collect(config('sukajadi.lead_sources'))
                ->mapWithKeys(fn (string $source): array => [__('site.lead_sources.'.$source) => $leads->where('source', $source)->count()])
                ->all(),
        ];
    }

    /** @return array{total: int, byType: array<string, int>, top: Collection} */
    protected function waStats(): array
    {
        [$start, $end] = $this->range();
        $base = WaClick::query()->whereBetween('created_at', [$start, $end]);

        $byType = (clone $base)
            ->selectRaw('context_type, count(*) as clicks')
            ->groupBy('context_type')
            ->pluck('clicks', 'context_type');

        $top = (clone $base)
            ->selectRaw('context_type, context_id, count(*) as clicks')
            ->groupBy('context_type', 'context_id')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get()
            ->map(fn (WaClick $row): array => [
                'type' => __('site.wa_contexts.'.$row->context_type),
                'name' => $this->contextName($row->context_type, $row->context_id),
                'clicks' => (int) $row->getAttribute('clicks'),
            ]);

        return [
            'total' => (int) $byType->sum(),
            'byType' => $byType->sortDesc()
                ->mapWithKeys(fn ($clicks, $type): array => [__('site.wa_contexts.'.$type) => (int) $clicks])
                ->all(),
            'top' => $top,
        ];
    }

    /** Nama yang mudah dibaca untuk konteks klik WhatsApp. */
    public function contextName(?string $type, int|string|null $id): string
    {
        if (blank($id)) {
            return $type === 'general' || blank($type) ? 'Tombol WhatsApp umum' : __('site.wa_contexts.'.$type);
        }

        $name = match ($type) {
            'package' => Package::query()->find($id)?->tr('name', 'id'),
            'attraction' => Attraction::query()->find($id)?->tr('name', 'id'),
            'umkm' => Umkm::query()->find($id)?->name,
            'product' => ($product = UmkmProduct::query()->with('umkm')->find($id))
                ? $product->tr('name', 'id').($product->umkm ? ' — '.$product->umkm->name : '')
                : null,
            'partner' => Partner::query()->find($id)?->name,
            'offer' => ($offer = PartnerOffer::query()->with('partner')->find($id))
                ? $offer->tr('name', 'id').($offer->partner ? ' — '.$offer->partner->name : '')
                : null,
            'booking' => Booking::query()->find($id)?->code,
            default => null,
        };

        return $name ?: __('site.wa_contexts.'.$type).' #'.$id.' (sudah dihapus)';
    }

    /** @return array<string, array{label: string, bookings: int, pax: int, revenue: int, cancelled: int}> */
    protected function salesByType(): array
    {
        [$start, $end] = $this->range();
        $bookings = Booking::query()->whereBetween('created_at', [$start, $end])->get(['type', 'status', 'pax', 'total']);

        return collect(Fields::bookingTypes())->map(function (string $label, string $type) use ($bookings): array {
            $items = $bookings->where('type', $type);
            $active = $items->where('status', '!=', 'cancelled');

            return [
                'label' => $label,
                'bookings' => $active->count(),
                'pax' => (int) $active->sum('pax'),
                'gross' => (int) $active->sum('total'),
                'revenue' => (int) $items->whereIn('status', Reports::REVENUE_STATUSES)->sum('total'),
                'cancelled' => $items->where('status', 'cancelled')->count(),
            ];
        })->all();
    }

    /**
     * Komisi per mitra untuk booking (tidak dibatalkan) yang masuk dalam periode.
     *
     * @return Collection<int, array{name: string, rate: ?float, bookings: int, gross: int, commission: int, pending: int, invoiced: int, paid: int}>
     */
    public function commissionPerPartner(): Collection
    {
        [$start, $end] = $this->range();

        $bookings = Booking::query()
            ->with('partner')
            ->whereNotNull('partner_id')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return $bookings
            ->groupBy('partner_id')
            ->map(fn (Collection $items): array => [
                'name' => $items->first()->partner?->name ?? 'Mitra dihapus',
                'rate' => $items->first()->partner?->commission_rate,
                'bookings' => $items->count(),
                'gross' => (int) $items->sum('total'),
                'commission' => (int) $items->sum('commission_amount'),
                'pending' => (int) $items->where('commission_status', 'pending')->sum('commission_amount'),
                'invoiced' => (int) $items->where('commission_status', 'invoiced')->sum('commission_amount'),
                'paid' => (int) $items->where('commission_status', 'paid')->sum('commission_amount'),
            ])
            ->sortByDesc('commission')
            ->values();
    }

    protected function getViewData(): array
    {
        $commission = $this->commissionPerPartner();

        return [
            'periods' => self::PERIODS,
            'leads' => $this->leadStats(),
            'wa' => $this->waStats(),
            'sales' => $this->salesByType(),
            'commission' => $commission,
            'commissionTotal' => [
                'bookings' => $commission->sum('bookings'),
                'gross' => $commission->sum('gross'),
                'commission' => $commission->sum('commission'),
                'pending' => $commission->sum('pending'),
                'invoiced' => $commission->sum('invoiced'),
                'paid' => $commission->sum('paid'),
            ],
        ];
    }

    public function exportCommissionCsv(): StreamedResponse
    {
        [$start, $end] = $this->range();
        $rows = $this->commissionPerPartner();
        $filename = 'komisi-mitra-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        $headers = [
            'Mitra', 'Komisi (%)', 'Booking', 'Penjualan kotor', 'Komisi', 'Belum ditagih', 'Sudah ditagih', 'Sudah dibayar',
        ];

        return response()->streamDownload(function () use ($rows, $headers): void {
            $out = fopen('php://output', 'w');
            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers, ';', '"', '');

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['name'],
                    $row['rate'] !== null ? str_replace('.', ',', (string) $row['rate']) : '',
                    $row['bookings'],
                    $row['gross'],
                    $row['commission'],
                    $row['pending'],
                    $row['invoiced'],
                    $row['paid'],
                ], ';', '"', '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
