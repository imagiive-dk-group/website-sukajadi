<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Super admin tidak boleh menonaktifkan / menurunkan peran akunnya sendiri.
        if ($this->record->getKey() === auth()->id()) {
            unset($data['is_active'], $data['role']);
        }

        return $data;
    }
}
