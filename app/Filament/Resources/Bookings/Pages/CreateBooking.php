<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected static ?string $title = 'Booking manual';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $quote = BookingResource::quote($data['package_tier_id'] ?? null, (int) $data['pax'], (array) ($data['addon_ids'] ?? []));
        unset($data['addon_ids'], $data['estimate']);

        return [
            ...$data,
            ...$quote,
            'code' => Booking::generateCode(),
            'type' => 'package',
            'confirmed_at' => in_array($data['status'] ?? 'new', ['confirmed', 'completed'], true) ? now() : null,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return BookingResource::getUrl('view', ['record' => $this->getRecord()]);
    }
}
