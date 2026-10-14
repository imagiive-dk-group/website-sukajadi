<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Support\Fields;
use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingVisits extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Kunjungan 14 hari ke depan';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Booking::query()
                ->whereIn('status', ['new', 'confirmed'])
                ->whereDate('visit_date', '>=', today())
                ->whereDate('visit_date', '<=', today()->addDays(14))
                ->orderBy('visit_date')
                ->orderBy('created_at'))
            ->paginated([10, 25])
            ->emptyStateHeading('Belum ada kunjungan dalam 14 hari ke depan')
            ->recordUrl(fn (Booking $record): string => BookingResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('visit_date')
                    ->label('Tanggal')
                    ->date('D, j M Y'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->weight('bold'),
                TextColumn::make('package_name')
                    ->label('Paket')
                    ->wrap(),
                TextColumn::make('pax')
                    ->label('Pax'),
                TextColumn::make('customer_name')
                    ->label('Pemesan')
                    ->description(fn (Booking $record): ?string => $record->organization),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __('site.status.'.$state))
                    ->color(fn (?string $state): string => Fields::statusColor($state)),
            ])
            ->recordActions([
                BookingResource::whatsappAction(),
            ]);
    }
}
