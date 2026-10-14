<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Support\Fields;
use App\Models\BlockedDate;
use App\Models\Booking;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use UnitEnum;

class BookingCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Booking';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Kalender Booking';

    protected static ?string $title = 'Kalender Booking';

    protected static ?string $slug = 'kalender-booking';

    protected string $view = 'filament.pages.booking-calendar';

    /** Bulan yang ditampilkan, format Y-m. */
    #[Url(as: 'bulan')]
    public string $month = '';

    public function mount(): void
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $this->month)) {
            $this->month = now()->format('Y-m');
        }
    }

    public function currentMonth(): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $this->month.'-01')->startOfDay();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    public function previousMonth(): void
    {
        $this->month = $this->currentMonth()->subMonthNoOverflow()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->currentMonth()->addMonthNoOverflow()->format('Y-m');
    }

    public function thisMonth(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->currentMonth()->translatedFormat('F Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previous')
                ->label('Bulan sebelumnya')
                ->icon(Heroicon::OutlinedChevronLeft)
                ->color('gray')
                ->action(fn () => $this->previousMonth()),
            Action::make('today')
                ->label('Bulan ini')
                ->color('gray')
                ->action(fn () => $this->thisMonth()),
            Action::make('next')
                ->label('Bulan berikutnya')
                ->icon(Heroicon::OutlinedChevronRight)
                ->iconPosition('after')
                ->color('gray')
                ->action(fn () => $this->nextMonth()),
        ];
    }

    /**
     * Data grid kalender: minggu dimulai hari Senin.
     */
    protected function getViewData(): array
    {
        $month = $this->currentMonth();
        $start = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $bookings = Booking::query()
            ->whereDate('visit_date', '>=', $start)
            ->whereDate('visit_date', '<=', $end)
            ->orderBy('visit_date')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn (Booking $booking): string => $booking->visit_date->format('Y-m-d'));

        $blocked = BlockedDate::query()
            ->with('package')
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->get()
            ->groupBy(fn (BlockedDate $date): string => $date->date->format('Y-m-d'));

        $days = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $dayBookings = $bookings->get($key, collect());

            $days[] = [
                'date' => $date->copy(),
                'inMonth' => $date->month === $month->month,
                'isToday' => $date->isToday(),
                'bookings' => $dayBookings->map(fn (Booking $booking): array => [
                    'code' => $booking->code,
                    'package' => $booking->package_name,
                    'pax' => $booking->pax,
                    'customer' => $booking->organization ?: $booking->customer_name,
                    'status' => $booking->status,
                    'statusLabel' => __('site.status.'.$booking->status),
                    'color' => Fields::statusColor($booking->status),
                    'url' => BookingResource::getUrl('edit', ['record' => $booking]),
                ])->all(),
                'blocked' => $blocked->get($key, collect())->map(fn (BlockedDate $item): string => trim(
                    ($item->package ? localized($item->package->name, 'id').': ' : 'Semua paket: ').($item->reason ?: 'ditutup')
                ))->all(),
                'pax' => (int) $dayBookings->where('status', '!=', 'cancelled')->sum('pax'),
            ];
        }

        $monthBookings = $bookings->flatten()->filter(fn (Booking $b): bool => $b->visit_date->month === $month->month && $b->status !== 'cancelled');

        return [
            'days' => $days,
            'weekdays' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'monthTotalPax' => (int) $monthBookings->sum('pax'),
            'monthTotalBookings' => $monthBookings->count(),
            'statuses' => collect(config('sukajadi.booking_statuses'))
                ->mapWithKeys(fn (string $status): array => [$status => ['label' => __('site.status.'.$status), 'color' => Fields::statusColor($status)]])
                ->all(),
        ];
    }
}
