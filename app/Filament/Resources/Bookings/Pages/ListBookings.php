<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Pages\BookingCalendar;
use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calendar')
                ->label('Kalender')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('gray')
                ->url(fn (): string => BookingCalendar::getUrl()),
            CreateAction::make()->label('Booking manual'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'new' => Tab::make('Baru')
                ->badge(fn (): int => BookingResource::getModel()::query()->where('status', 'new')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'new')),
            'upcoming' => Tab::make('Akan datang')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereIn('status', ['new', 'confirmed'])
                    ->whereDate('visit_date', '>=', today())),
            'confirmed' => Tab::make('Dikonfirmasi')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'confirmed')),
            'completed' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'completed')),
            'cancelled' => Tab::make('Dibatalkan')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'cancelled')),
        ];
    }
}
