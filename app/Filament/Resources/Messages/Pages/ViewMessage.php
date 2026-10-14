<?php

namespace App\Filament\Resources\Messages\Pages;

use App\Filament\Resources\Messages\MessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Membuka pesan = menandai sudah dibaca.
        if ($this->record->read_at === null) {
            $this->record->forceFill(['read_at' => now()])->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            MessageResource::replyAction(),
            Action::make('markUnread')
                ->label('Tandai belum dibaca')
                ->icon(Heroicon::OutlinedEnvelope)
                ->color('gray')
                ->action(function (): void {
                    $this->record->forceFill(['read_at' => null])->save();
                    $this->redirect(MessageResource::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }
}
