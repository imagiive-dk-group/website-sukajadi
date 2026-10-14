<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Messages\MessageResource;
use App\Models\Booking;
use App\Models\Message;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $new = Booking::query()->where('status', 'new')->count();

        $thisMonth = Booking::query()
            ->holdingCapacity()
            ->whereDate('visit_date', '>=', now()->startOfMonth())
            ->whereDate('visit_date', '<=', now()->endOfMonth())
            ->count();

        $upcomingPax = (int) Booking::query()
            ->holdingCapacity()
            ->where('status', '!=', 'completed')
            ->whereDate('visit_date', '>=', today())
            ->whereDate('visit_date', '<=', today()->addDays(30))
            ->sum('pax');

        $unread = Message::query()->whereNull('read_at')->count();

        return [
            Stat::make('Booking baru', $new)
                ->description($new ? 'Menunggu konfirmasi via WhatsApp' : 'Semua sudah ditangani')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($new ? 'warning' : 'success')
                ->url(BookingResource::getUrl('index', ['activeTab' => 'new'])),
            Stat::make('Kunjungan bulan ini', $thisMonth)
                ->description(now()->translatedFormat('F Y').' (tanpa yang dibatalkan)')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
            Stat::make('Peserta 30 hari ke depan', number_format($upcomingPax, 0, ',', '.').' pax')
                ->description('Booking baru & dikonfirmasi')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('info'),
            Stat::make('Pesan belum dibaca', $unread)
                ->description('Dari formulir kontak')
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->color($unread ? 'warning' : 'gray')
                ->url(MessageResource::getUrl('index')),
        ];
    }
}
