<?php

namespace App\Filament\Resources\ProfileStats\Pages;

use App\Filament\Resources\ProfileStats\ProfileStatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfileStat extends EditRecord
{
    protected static string $resource = ProfileStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
