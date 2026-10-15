<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Monitoring;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Message;
use App\Models\WaClick;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|array|null $columns = 3;

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
        $newLeads = Lead::query()->where('status', 'new')->count();

        $leads30 = Lead::query()->where('created_at', '>=', today()->subDays(29));
        $leadsTotal = (clone $leads30)->count();
        $leadsWon = (clone $leads30)->where('status', 'won')->count();
        $conversion = $leadsTotal ? round($leadsWon / $leadsTotal * 100, 1) : 0;

        $waClicks = WaClick::query()->where('created_at', '>=', today()->subDays(6))->count();
        $waPrevious = WaClick::query()
            ->where('created_at', '>=', today()->subDays(13))
            ->where('created_at', '<', today()->subDays(6))
            ->count();

        $commissionPending = (int) Booking::query()
            ->where('status', '!=', 'cancelled')
            ->where('commission_status', 'pending')
            ->sum('commission_amount');

        return [
            Stat::make('Booking baru', $new)
                ->description($new ? 'Menunggu konfirmasi via WhatsApp' : 'Semua sudah ditangani')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($new ? 'warning' : 'success')
                ->url(BookingResource::getUrl('index', ['tab' => 'new'])),
            Stat::make('Kunjungan bulan ini', $thisMonth)
                ->description(number_format($upcomingPax, 0, ',', '.').' pax dalam 30 hari ke depan')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
            Stat::make('Prospek baru', $newLeads)
                ->description($unread ? $unread.' pesan kontak belum dibaca' : 'Belum dihubungi')
                ->descriptionIcon($unread ? Heroicon::OutlinedEnvelope : Heroicon::OutlinedUserPlus)
                ->color($newLeads || $unread ? 'warning' : 'gray')
                ->url(LeadResource::getUrl('index', ['tab' => 'new'])),
            Stat::make('Konversi 30 hari', number_format($conversion, 1, ',', '.').'%')
                ->description($leadsWon.' dari '.$leadsTotal.' prospek menjadi booking')
                ->descriptionIcon(Heroicon::OutlinedFunnel)
                ->color($conversion >= 30 ? 'success' : 'info')
                ->url(Monitoring::getUrl()),
            Stat::make('Klik WhatsApp 7 hari', $waClicks)
                ->description('7 hari sebelumnya: '.$waPrevious)
                ->descriptionIcon($waClicks >= $waPrevious ? Heroicon::OutlinedArrowTrendingUp : Heroicon::OutlinedArrowTrendingDown)
                ->color('success')
                ->url(Monitoring::getUrl(['periode' => '30d'])),
            Stat::make('Komisi belum ditagih', rupiah($commissionPending))
                ->description('Dari booking mitra bisnis')
                ->descriptionIcon(Heroicon::OutlinedReceiptPercent)
                ->color($commissionPending ? 'warning' : 'gray')
                ->url(BookingResource::getUrl('index', ['filters' => ['commission_status' => ['values' => ['pending']]]])),
        ];
    }
}
