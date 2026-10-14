<?php

namespace App\Filament\Resources\Attractions;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Attractions\Pages\CreateAttraction;
use App\Filament\Resources\Attractions\Pages\EditAttraction;
use App\Filament\Resources\Attractions\Pages\ListAttractions;
use App\Filament\Support\Fields;
use App\Models\Attraction;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AttractionResource extends Resource
{
    protected static ?string $model = Attraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Wisata';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Destinasi & Aktivitas';

    protected static ?string $modelLabel = 'destinasi';

    protected static ?string $pluralModelLabel = 'destinasi & aktivitas';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->name, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi destinasi')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::bilingual(fn (string $locale): array => [
                            Fields::slugSource(
                                TextInput::make("name.$locale")
                                    ->label('Nama destinasi / aktivitas')
                                    ->required($locale === 'id')
                                    ->maxLength(255),
                                $locale === 'id' ? 'slug' : 'slug_en',
                                'name.en',
                            ),
                            Textarea::make("summary.$locale")
                                ->label('Ringkasan singkat')
                                ->rows(2)
                                ->maxLength(500),
                            Fields::richEditor("description.$locale")
                                ->label('Deskripsi lengkap'),
                        ]),
                        ...Fields::slugFields(),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(Fields::options('attraction_categories', 'site.categories'))
                            ->default('alam')
                            ->required(),
                        TextInput::make('custom_price')
                            ->label('Harga paket custom (per pax)')
                            ->helperText('Harga per pax bila dipilih di Paket Custom; kosongkan bila tidak tersedia')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Grid::make(2)->schema([
                            Toggle::make('is_featured')->label('Unggulan (tampil di beranda)'),
                            Toggle::make('is_active')->label('Aktif (tampil di website)')->default(true),
                        ]),
                    ]),

                Section::make('Foto sampul')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::image('cover_path', 'attractions', 'Foto sampul'),
                        Fields::placeholderLabel(),
                    ]),

                Section::make('Paket terkait')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('packages')
                            ->label('Termasuk dalam paket')
                            ->relationship('packages', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => (string) localized($record->name, 'id'))
                            ->multiple()
                            ->preload(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::imageColumn(),
                Fields::trColumn('name', 'Nama'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => __('site.categories.'.$state))
                    ->badge(),
                TextColumn::make('custom_price')
                    ->label('Harga custom')
                    ->formatStateUsing(fn ($state): string => rupiah($state).'/pax')
                    ->placeholder('Tidak tersedia'),
                TextColumn::make('packages_count')
                    ->label('Paket')
                    ->counts('packages'),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Fields::options('attraction_categories', 'site.categories')),
                TernaryFilter::make('is_active')->label('Aktif'),
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
            'index' => ListAttractions::route('/'),
            'create' => CreateAttraction::route('/create'),
            'edit' => EditAttraction::route('/{record}/edit'),
        ];
    }
}
