<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            BookingResource::whatsappAction(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $pax = (int) ($data['pax'] ?? $this->record->pax);

        if ($pax !== (int) $this->record->pax) {
            $data = [...$data, ...BookingResource::recalculate($this->record, $pax)];
        }

        if (in_array($data['status'] ?? null, ['confirmed', 'completed'], true) && $this->record->confirmed_at === null) {
            $data['confirmed_at'] = now();
        }

        return $data;
    }
}
