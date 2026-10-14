<?php

namespace App\Filament\Resources\Umkms;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Umkms\Pages\CreateUmkm;
use App\Filament\Resources\Umkms\Pages\EditUmkm;
use App\Filament\Resources\Umkms\Pages\ListUmkms;
use App\Filament\Resources\Umkms\RelationManagers\ProductsRelationManager;
use App\Filament\Support\Fields;
use App\Models\Umkm;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UmkmResource extends Resource
{
    protected static ?string $model = Umkm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'UMKM';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Direktori UMKM';

    protected static ?string $modelLabel = 'UMKM';

    protected static ?string $pluralModelLabel = 'UMKM';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil usaha')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama usaha')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, string $operation, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', str($state)->slug()->toString());
                                    $set('slug_en', str($state)->slug()->toString());
                                }
                            }),
                        TextInput::make('owner_name')
                            ->label('Nama pemilik')
                            ->maxLength(255),
                        ...Fields::slugFields(),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(Fields::options('umkm_categories', 'site.umkm_categories'))
                            ->default('makanan')
                            ->required(),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Fields::bilingual(fn (string $locale): array => [
                            Textarea::make("summary.$locale")
                                ->label('Ringkasan (satu kalimat)')
                                ->rows(2)
                                ->maxLength(500),
                            Fields::richEditor("description.$locale")
                                ->label('Deskripsi usaha'),
                            TextInput::make("opening_hours.$locale")
                                ->label('Jam buka')
                                ->placeholder($locale === 'id' ? 'Misal: Setiap hari, 08.00–16.00' : 'e.g. Daily, 08:00–16:00')
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Foto sampul')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::image('cover_path', 'umkm', 'Foto sampul / logo'),
                        Fields::placeholderLabel(),
                    ]),

                Section::make('Kontak & lokasi')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('show_contact')
                            ->label('Tampilkan kontak UMKM di website')
                            ->helperText('Aktifkan hanya jika pemilik sudah memberi izin. Jika nonaktif, tombol WA diarahkan ke admin desa.')
                            ->columnSpanFull(),
                        TextInput::make('whatsapp')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->placeholder('0857xxxxxxx')
                            ->maxLength(30),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->maxLength(100),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->maxLength(255),
                        TextInput::make('maps_url')
                            ->label('Link Google Maps')
                            ->url()
                            ->maxLength(1000),
                    ]),

                Section::make('Layanan & tampilan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('accepts_orders')->label('Menerima pesanan')->default(true),
                            Toggle::make('accepts_visits')->label('Menerima kunjungan'),
                            Toggle::make('is_featured')->label('Unggulan (tampil di beranda)'),
                            Toggle::make('is_active')->label('Aktif (tampil di website)')->default(true),
                        ])->columnSpanFull(),
                        Select::make('packages')
                            ->label('Terlibat dalam paket')
                            ->relationship('packages', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => (string) localized($record->name, 'id'))
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
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
                TextColumn::make('name')
                    ->label('Nama usaha')
                    ->description(fn (Umkm $record): ?string => $record->owner_name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => __('site.umkm_categories.'.$state))
                    ->badge(),
                TextColumn::make('products_count')
                    ->label('Produk')
                    ->counts('products'),
                TextColumn::make('contact_status')
                    ->label('Kontak di website')
                    ->getStateUsing(fn (Umkm $record): string => $record->hasPublicContact() ? 'Publik' : 'Lewat admin desa')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Publik' ? 'success' : 'gray'),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Fields::options('umkm_categories', 'site.umkm_categories')),
                TernaryFilter::make('show_contact')->label('Kontak ditampilkan'),
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
            ProductsRelationManager::class,
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkms::route('/'),
            'create' => CreateUmkm::route('/create'),
            'edit' => EditUmkm::route('/{record}/edit'),
        ];
    }
}
