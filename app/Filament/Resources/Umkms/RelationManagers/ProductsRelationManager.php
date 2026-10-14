<?php

namespace App\Filament\Resources\Umkms\RelationManagers;

use App\Filament\Support\Fields;
use App\Models\UmkmProduct;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Produk';

    protected static ?string $modelLabel = 'produk';

    protected static ?string $pluralModelLabel = 'produk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fields::bilingual(fn (string $locale): array => [
                    TextInput::make("name.$locale")
                        ->label('Nama produk')
                        ->required($locale === 'id')
                        ->maxLength(255),
                    Textarea::make("description.$locale")
                        ->label('Keterangan')
                        ->rows(2),
                    TextInput::make("unit.$locale")
                        ->label('Satuan')
                        ->placeholder($locale === 'id' ? 'Misal: botol, pak, kg' : 'e.g. bottle, pack, kg')
                        ->maxLength(50),
                ], 'product-translations'),
                Grid::make(2)->schema([
                    TextInput::make('price_min')
                        ->label('Harga mulai')
                        ->prefix('Rp')
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('price_max')
                        ->label('Harga sampai (opsional)')
                        ->prefix('Rp')
                        ->numeric()
                        ->minValue(0),
                ])->columnSpanFull(),
                Fields::image('image_path', 'umkm/products', 'Foto produk'),
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
                Fields::trColumn('name', 'Nama produk'),
                TextColumn::make('price_label')
                    ->label('Harga')
                    ->getStateUsing(fn (UmkmProduct $record): ?string => $record->priceLabel())
                    ->placeholder('-'),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah produk'),
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
