<?php

namespace App\Filament\RelationManagers;

use App\Filament\Support\Fields;
use App\Models\Photo;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Galeri foto (relasi polimorfik photoable) untuk Paket, Destinasi, UMKM, dan Album.
 */
class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Galeri Foto';

    protected static ?string $modelLabel = 'foto';

    protected static ?string $pluralModelLabel = 'foto';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fields::image('path', $this->photoDirectory())
                    ->columnSpanFull(),
                Fields::placeholderLabel()
                    ->columnSpanFull(),
                TextInput::make('caption.id')
                    ->label('Keterangan foto (Indonesia)')
                    ->maxLength(255),
                TextInput::make('caption.en')
                    ->label('Keterangan foto (English)')
                    ->maxLength(255),
                TextInput::make('sort')
                    ->label('Urutan')
                    ->numeric()
                    ->minValue(0)
                    ->default(fn (): int => $this->nextSort()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('placeholder_label')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::imageColumn('path'),
                TextColumn::make('placeholder_label')
                    ->label('Keterangan foto yang dibutuhkan')
                    ->wrap()
                    ->placeholder('-'),
                TextColumn::make('caption')
                    ->label('Keterangan (ID)')
                    ->getStateUsing(fn (Photo $record): ?string => localized($record->caption, 'id'))
                    ->wrap()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn (Photo $record): string => filled($record->path) ? 'Sudah ada foto' : 'Belum ada foto')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Sudah ada foto' ? 'success' : 'gray'),
            ])
            ->headerActions([
                $this->bulkUploadAction(),
                CreateAction::make()->label('Tambah foto'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function bulkUploadAction(): Action
    {
        return Action::make('bulkUpload')
            ->label('Unggah banyak foto')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->color('primary')
            ->modalHeading('Unggah banyak foto')
            ->modalSubmitActionLabel('Simpan foto')
            ->schema([
                FileUpload::make('files')
                    ->label('Foto')
                    ->disk('uploads')
                    ->directory($this->photoDirectory())
                    ->visibility('public')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->maxSize(10240)
                    ->maxParallelUploads(3)
                    ->required()
                    ->helperText('Pilih beberapa foto sekaligus (maks. 10 MB per foto). Setiap foto menjadi satu item galeri.'),
                TextInput::make('placeholder_label')
                    ->label('Keterangan foto (opsional, untuk semua foto)')
                    ->maxLength(255),
            ])
            ->action(function (array $data): void {
                $sort = $this->nextSort();
                $files = array_values((array) ($data['files'] ?? []));

                foreach ($files as $path) {
                    $this->getOwnerRecord()->photos()->create([
                        'path' => $path,
                        'placeholder_label' => $data['placeholder_label'] ?? null,
                        'sort' => $sort++,
                    ]);
                }

                Notification::make()
                    ->title(count($files).' foto berhasil diunggah')
                    ->success()
                    ->send();
            });
    }

    protected function nextSort(): int
    {
        return ((int) $this->getOwnerRecord()->photos()->max('sort')) + 1;
    }

    protected function photoDirectory(): string
    {
        return 'photos/'.str(class_basename($this->getOwnerRecord()))->snake()->plural();
    }
}
