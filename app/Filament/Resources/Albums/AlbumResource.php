<?php

namespace App\Filament\Resources\Albums;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Albums\Pages\CreateAlbum;
use App\Filament\Resources\Albums\Pages\EditAlbum;
use App\Filament\Resources\Albums\Pages\ListAlbums;
use App\Filament\Support\Fields;
use App\Models\Album;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Galeri / Album';

    protected static ?string $modelLabel = 'album';

    protected static ?string $pluralModelLabel = 'galeri / album';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->title, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Album foto')
                    ->description('Simpan album dulu, lalu unggah foto di bagian "Galeri Foto" di bawah.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::bilingual(fn (string $locale): array => [
                            Fields::slugSource(
                                TextInput::make("title.$locale")
                                    ->label('Judul album')
                                    ->required($locale === 'id')
                                    ->maxLength(255),
                                $locale === 'id' ? 'slug' : 'slug_en',
                                'title.en',
                            ),
                            Textarea::make("description.$locale")
                                ->label('Keterangan')
                                ->rows(3),
                        ]),
                        ...Fields::slugFields(),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(Fields::options('album_categories', 'site.categories'))
                            ->default('alam')
                            ->required(),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Aktif (tampil di website)')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::trColumn('title', 'Judul album'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => __('site.categories.'.$state))
                    ->badge(),
                TextColumn::make('photos_count')
                    ->label('Jumlah foto')
                    ->counts('photos')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Fields::options('album_categories', 'site.categories')),
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

    public static function getRelations(): array
    {
        return [
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlbums::route('/'),
            'create' => CreateAlbum::route('/create'),
            'edit' => EditAlbum::route('/{record}/edit'),
        ];
    }
}
