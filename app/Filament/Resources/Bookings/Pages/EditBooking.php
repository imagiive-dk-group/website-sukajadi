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
        $nights = array_key_exists('nights', $data) && $this->record->nights !== null ? max(1, (int) $data['nights']) : $this->record->nights;

        if ($pax !== (int) $this->record->pax || $nights !== $this->record->nights) {
            $data = [...$data, ...BookingResource::recalculate($this->record, $pax, $nights)];
        }

        if ($nights !== null && $nights !== $this->record->nights) {
            $data['package_name'] = preg_replace('/\(\d+ malam\)$/', '('.$nights.' malam)', (string) $this->record->package_name);
        }

        if (in_array($data['status'] ?? null, ['confirmed', 'completed'], true) && $this->record->confirmed_at === null) {
            $data['confirmed_at'] = now();
        }

        return $data;
    }
}
