<?php

namespace App\Filament\Resources\ProfileStats\Pages;

use App\Filament\Resources\ProfileStats\ProfileStatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfileStats extends ListRecords
{
    protected static string $resource = ProfileStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
