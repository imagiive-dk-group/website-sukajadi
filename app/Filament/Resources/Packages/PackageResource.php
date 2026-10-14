<?php

namespace App\Filament\Resources\Packages;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Support\Fields;
use App\Models\Package;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Wisata';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Paket Wisata';

    protected static ?string $modelLabel = 'paket wisata';

    protected static ?string $pluralModelLabel = 'paket wisata';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->name, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi paket')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::bilingual(fn (string $locale): array => [
                            Fields::slugSource(
                                TextInput::make("name.$locale")
                                    ->label('Nama paket')
                                    ->required($locale === 'id')
                                    ->maxLength(255),
                                $locale === 'id' ? 'slug' : 'slug_en',
                                'name.en',
                            ),
                            Textarea::make("summary.$locale")
                                ->label('Ringkasan singkat')
                                ->rows(2)
                                ->maxLength(500),
                            TextInput::make("duration.$locale")
                                ->label('Durasi')
                                ->placeholder($locale === 'id' ? 'Misal: 4–5 jam' : 'e.g. 4–5 hours')
                                ->maxLength(100),
                            Fields::richEditor("description.$locale")
                                ->label('Deskripsi lengkap'),
                        ]),
                        ...Fields::slugFields(),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(Fields::options('package_categories', 'site.categories'))
                            ->default('edukasi')
                            ->required(),
                        TextInput::make('daily_capacity')
                            ->label('Kuota harian (pax)')
                            ->helperText('Kuota pax per hari untuk paket ini; kosongkan = tidak dibatasi')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Grid::make(2)->schema([
                            Toggle::make('is_featured')->label('Paket unggulan (tampil di beranda)'),
                            Toggle::make('is_active')->label('Aktif (tampil di website)')->default(true),
                        ]),
                    ]),

                Section::make('Foto sampul')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::image('cover_path', 'packages', 'Foto sampul'),
                        Fields::placeholderLabel(),
                    ]),

                Section::make('Fasilitas & sasaran pengunjung')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        CheckboxList::make('facilities')
                            ->label('Fasilitas')
                            ->options(Fields::options('facilities', 'site.facilities')),
                        CheckboxList::make('target_groups')
                            ->label('Cocok untuk')
                            ->options(Fields::options('group_types', 'site.group_types')),
                    ]),

                Section::make('Tingkat harga')
                    ->description('Semua harga per pax. Tandai "Harga sementara" bila harga belum final.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('tiers')
                            ->label('Tingkat harga')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->reorderable()
                            ->collapsible()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah tingkat harga')
                            ->itemLabel(fn (array $state): string => (($state['name']['id'] ?? null) ?: 'Tingkat baru')
                                .(filled($state['price'] ?? null) ? ' · '.rupiah((int) $state['price']).'/pax' : '')
                                .(($state['is_dummy_price'] ?? false) ? ' · harga sementara' : ''))
                            ->schema(static::tierSchema()),
                    ]),

                Section::make('Rundown kegiatan')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Repeater::make('itinerary')
                            ->hiddenLabel()
                            ->reorderable()
                            ->defaultItems(0)
                            ->addActionLabel('Tambah kegiatan')
                            ->columns(3)
                            ->schema([
                                TextInput::make('time')
                                    ->label('Waktu')
                                    ->placeholder('08.00')
                                    ->maxLength(50),
                                TextInput::make('activity.id')
                                    ->label('Kegiatan (Indonesia)')
                                    ->required(),
                                TextInput::make('activity.en')
                                    ->label('Kegiatan (English)'),
                            ]),
                    ]),

                Section::make('Destinasi & UMKM terkait')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('attractions')
                            ->label('Destinasi / aktivitas')
                            ->relationship('attractions', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => (string) localized($record->name, 'id'))
                            ->multiple()
                            ->preload(),
                        Select::make('umkms')
                            ->label('UMKM yang terlibat')
                            ->relationship('umkms', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    /** @return array<\Filament\Schemas\Components\Component> */
    public static function tierSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name.id')
                    ->label('Nama tingkat (Indonesia)')
                    ->placeholder('Hemat / Standar / Premium')
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('name.en')
                    ->label('Nama tingkat (English)'),
                TextInput::make('duration.id')
                    ->label('Durasi (Indonesia)'),
                TextInput::make('duration.en')
                    ->label('Durasi (English)'),
                TextInput::make('badge.id')
                    ->label('Badge (Indonesia)')
                    ->helperText('Misal "Best Seller". Kosongkan bila tidak ada.'),
                TextInput::make('badge.en')
                    ->label('Badge (English)'),
            ]),
            Grid::make(3)->schema([
                TextInput::make('price')
                    ->label('Harga per pax')
                    ->prefix('Rp')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('min_pax')
                    ->label('Minimal peserta (pax)')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                Toggle::make('is_dummy_price')
                    ->label('Harga sementara (dummy)')
                    ->helperText('Tampil label "Harga sementara".')
                    ->inline(false),
            ]),
            Repeater::make('inclusions')
                ->label('Termasuk dalam tingkat ini')
                ->reorderable()
                ->defaultItems(0)
                ->addActionLabel('Tambah item')
                ->columns(2)
                ->schema([
                    TextInput::make('id')
                        ->label('Indonesia')
                        ->required(),
                    TextInput::make('en')
                        ->label('English'),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('tiers'))
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::imageColumn(),
                Fields::trColumn('name', 'Nama paket'),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => __('site.categories.'.$state))
                    ->badge(),
                TextColumn::make('starting_price')
                    ->label('Mulai dari')
                    ->getStateUsing(fn (Package $record): ?string => $record->startingPrice() !== null ? rupiah($record->startingPrice()) : null)
                    ->placeholder('-'),
                TextColumn::make('dummy_price')
                    ->label('Harga')
                    ->getStateUsing(fn (Package $record): ?string => $record->tiers->contains('is_dummy_price', true) ? 'Harga sementara' : null)
                    ->badge()
                    ->color('warning')
                    ->placeholder('Final'),
                TextColumn::make('tiers_count')
                    ->label('Tingkat')
                    ->counts('tiers'),
                TextColumn::make('daily_capacity')
                    ->label('Kuota/hari')
                    ->placeholder('Tak dibatasi')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Fields::options('package_categories', 'site.categories')),
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
            'index' => ListPackages::route('/'),
            'create' => CreatePackage::route('/create'),
            'edit' => EditPackage::route('/{record}/edit'),
        ];
    }
}
