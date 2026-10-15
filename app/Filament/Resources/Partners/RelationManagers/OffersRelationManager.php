<?php

namespace App\Filament\Resources\Partners\RelationManagers;

use App\Filament\Support\Fields;
use App\Models\PartnerOffer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

/**
 * Penawaran/produk mitra yang bisa dibooking lewat website (tiket, kamar, menu, dll.).
 */
class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $title = 'Penawaran';

    protected static ?string $modelLabel = 'penawaran';

    protected static ?string $pluralModelLabel = 'penawaran';

    /** @return array<string, string> */
    public static function unitOptions(): array
    {
        return Fields::options('partner_units', 'site.units');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fields::bilingual(fn (string $locale): array => [
                    TextInput::make("name.$locale")
                        ->label('Nama penawaran')
                        ->placeholder($locale === 'id' ? 'Misal: Tiket masuk dewasa, Vila 2 kamar' : 'e.g. Adult entrance ticket, 2-bedroom villa')
                        ->required($locale === 'id')
                        ->maxLength(255),
                    Textarea::make("description.$locale")
                        ->label('Keterangan')
                        ->rows(2),
                ], 'offer-translations'),
                Grid::make(3)->schema([
                    TextInput::make('price')
                        ->label('Harga')
                        ->prefix('Rp')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    Select::make('unit')
                        ->label('Satuan harga')
                        ->options(static::unitOptions())
                        ->default('per_pax')
                        ->required(),
                    TextInput::make('min_qty')
                        ->label('Minimal pesan')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),
                ])->columnSpanFull(),
                Toggle::make('is_dummy_price')
                    ->label('Harga sementara')
                    ->helperText('Aktifkan bila harga belum dipastikan dengan mitra. Website menampilkan keterangan "harga dapat berubah".')
                    ->columnSpanFull(),
                Fields::image('image_path', 'partners/offers', 'Foto penawaran'),
                Fields::placeholderLabel(),
                TextInput::make('sort')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::imageColumn('image_path'),
                Fields::trColumn('name', 'Nama penawaran'),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state, PartnerOffer $record): string => rupiah($state).' '.__('site.units.'.$record->unit))
                    ->description(fn (PartnerOffer $record): ?string => $record->min_qty > 1 ? 'Min. '.$record->min_qty : null)
                    ->sortable(),
                TextColumn::make('price_status')
                    ->label('Status harga')
                    ->getStateUsing(fn (PartnerOffer $record): ?string => $record->is_dummy_price ? 'Harga sementara' : null)
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah penawaran'),
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
}
