<?php

namespace App\Filament\Resources\Partners;

use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\Partners\RelationManagers\OffersRelationManager;
use App\Filament\Support\Fields;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
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
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Mitra Bisnis';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Mitra Bisnis';

    protected static ?string $modelLabel = 'mitra bisnis';

    protected static ?string $pluralModelLabel = 'mitra bisnis';

    protected static ?string $recordTitleAttribute = 'name';

    /** @return array<string, string> */
    public static function agreementOptions(): array
    {
        return ['draft' => 'Draf', 'active' => 'Aktif', 'ended' => 'Berakhir'];
    }

    public static function agreementColor(?string $state): string
    {
        return match ($state) {
            'active' => 'success',
            'ended' => 'danger',
            default => 'gray',
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil mitra')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama mitra')
                            ->placeholder('Misal: Curug Nangka, Vila Highland Tamansari')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, string $operation, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', str($state)->slug()->toString());
                                    $set('slug_en', str($state)->slug()->toString());
                                }
                            }),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(Fields::options('partner_categories', 'site.partner_categories'))
                            ->default('wisata')
                            ->required(),
                        ...Fields::slugFields(),
                        TextInput::make('commission_rate')
                            ->label('Komisi')
                            ->helperText('Komisi dari setiap penjualan lewat website. 0 = tanpa komisi.')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
                            ->suffix('%')
                            ->default(10)
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
                                ->label('Deskripsi'),
                            TextInput::make("opening_hours.$locale")
                                ->label('Jam buka')
                                ->placeholder($locale === 'id' ? 'Misal: Setiap hari, 08.00–17.00' : 'e.g. Daily, 08:00–17:00')
                                ->maxLength(255),
                        ], 'partner-translations'),
                        Repeater::make('highlights')
                            ->label('Keunggulan / fasilitas')
                            ->helperText('Poin singkat yang tampil sebagai daftar di halaman mitra.')
                            ->schema([
                                TextInput::make('id')
                                    ->label('Indonesia')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('en')
                                    ->label('English')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Tambah keunggulan')
                            ->reorderable()
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),

                Section::make('Foto sampul')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::image('cover_path', 'partners', 'Foto sampul'),
                        Fields::placeholderLabel(),
                    ]),

                Section::make('Lokasi & tampilan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('address')
                            ->label('Alamat')
                            ->maxLength(255),
                        TextInput::make('maps_url')
                            ->label('Link Google Maps')
                            ->url()
                            ->maxLength(1000),
                        Grid::make(2)->schema([
                            Toggle::make('is_featured')->label('Unggulan (tampil di beranda)'),
                            Toggle::make('is_active')->label('Aktif (tampil di website)')->default(true),
                        ])->columnSpanFull(),
                    ]),

                Section::make('Data internal (tidak tampil di website)')
                    ->description('Kontak pengelola mitra & status perjanjian kerja sama.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('contact_name')
                            ->label('Nama kontak mitra')
                            ->maxLength(255),
                        TextInput::make('contact_phone')
                            ->label('No. WhatsApp kontak')
                            ->tel()
                            ->maxLength(30),
                        Select::make('agreement_status')
                            ->label('Status perjanjian')
                            ->options(static::agreementOptions())
                            ->default('draft')
                            ->required(),
                        Textarea::make('internal_notes')
                            ->label('Catatan internal')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->withCount([
                    'offers',
                    'bookings' => fn (Builder $q): Builder => $q->where('status', '!=', 'cancelled'),
                ])
                ->withSum(['bookings as commission_total' => fn (Builder $q): Builder => $q->where('status', '!=', 'cancelled')], 'commission_amount'))
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::imageColumn(),
                TextColumn::make('name')
                    ->label('Nama mitra')
                    ->description(fn (Partner $record): ?string => $record->contact_name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): string => __('site.partner_categories.'.$state))
                    ->badge(),
                TextColumn::make('commission_rate')
                    ->label('Komisi')
                    ->formatStateUsing(fn ($state): string => rtrim(rtrim(number_format((float) $state, 2, ',', '.'), '0'), ',').'%')
                    ->sortable(),
                TextColumn::make('agreement_status')
                    ->label('Perjanjian')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => static::agreementOptions()[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => static::agreementColor($state)),
                TextColumn::make('offers_count')
                    ->label('Penawaran')
                    ->numeric(),
                TextColumn::make('bookings_count')
                    ->label('Booking')
                    ->tooltip('Tanpa booking yang dibatalkan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('commission_total')
                    ->label('Total komisi')
                    ->formatStateUsing(fn ($state): string => rupiah((int) $state))
                    ->default(0)
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Fields::options('partner_categories', 'site.partner_categories')),
                SelectFilter::make('agreement_status')
                    ->label('Status perjanjian')
                    ->options(static::agreementOptions()),
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
            OffersRelationManager::class,
            PhotosRelationManager::class,
            BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }
}
