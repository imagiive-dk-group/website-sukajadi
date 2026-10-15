<?php

namespace App\Filament\Resources\Partners\RelationManagers;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Support\Fields;
use App\Models\Booking;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Daftar booking mitra (hanya baca). Status komisi diubah dari menu Booking.
 */
class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'Booking & Komisi';

    protected static ?string $modelLabel = 'booking';

    protected static ?string $pluralModelLabel = 'booking';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->defaultSort('visit_date', 'desc')
            ->recordUrl(fn (Booking $record): string => BookingResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('visit_date')
                    ->label('Tanggal')
                    ->date('j M Y')
                    ->sortable(),
                TextColumn::make('package_name')
                    ->label('Penawaran')
                    ->wrap()
                    ->limit(40),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __('site.status.'.$state))
                    ->color(fn (?string $state): string => Fields::statusColor($state)),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => rupiah($state))
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Komisi')
                    ->formatStateUsing(fn ($state): string => rupiah($state))
                    ->sortable(),
                TextColumn::make('commission_status')
                    ->label('Status komisi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __('site.commission_statuses.'.$state))
                    ->color(fn (?string $state): string => Fields::commissionColor($state)),
            ]);
    }
}
