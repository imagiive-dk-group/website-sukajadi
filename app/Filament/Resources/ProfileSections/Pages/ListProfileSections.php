<?php

namespace App\Filament\Resources\ProfileSections\Pages;

use App\Filament\Resources\ProfileSections\ProfileSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfileSections extends ListRecords
{
    protected static string $resource = ProfileSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
