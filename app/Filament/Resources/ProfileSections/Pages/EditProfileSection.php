<?php

namespace App\Filament\Resources\ProfileSections\Pages;

use App\Filament\Resources\ProfileSections\ProfileSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfileSection extends EditRecord
{
    protected static string $resource = ProfileSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
