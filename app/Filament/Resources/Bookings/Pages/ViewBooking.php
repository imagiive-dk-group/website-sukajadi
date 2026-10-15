<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            BookingResource::whatsappAction(),
            BookingResource::confirmAction(),
            BookingResource::completeAction(),
            BookingResource::cancelAction(),
            BookingResource::commissionAction('invoiced'),
            BookingResource::commissionAction('paid'),
            EditAction::make(),
        ];
    }
}
