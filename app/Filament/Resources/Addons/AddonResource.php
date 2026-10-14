<?php

namespace App\Filament\Resources\Addons;

use App\Filament\Resources\Addons\Pages\CreateAddon;
use App\Filament\Resources\Addons\Pages\EditAddon;
use App\Filament\Resources\Addons\Pages\ListAddons;
use App\Filament\Support\Fields;
use App\Models\Addon;
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
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AddonResource extends Resource
{
    public const UNITS = [
        'per_pax' => 'per pax',
        'per_group' => 'per rombongan',
    ];

    protected static ?string $model = Addon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Wisata';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tambahan Booking';

    protected static ?string $modelLabel = 'tambahan booking';

    protected static ?string $pluralModelLabel = 'tambahan booking';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->name, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tambahan (add-on) yang bisa dipilih saat booking')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::bilingual(fn (string $locale): array => [
                            TextInput::make("name.$locale")
                                ->label('Nama tambahan')
                                ->required($locale === 'id')
                                ->maxLength(255),
                            Textarea::make("description.$locale")
                                ->label('Keterangan')
                                ->rows(2),
                        ]),
                        TextInput::make('price')
                            ->label('Harga')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Select::make('unit')
                            ->label('Satuan harga')
                            ->options(self::UNITS)
                            ->default('per_pax')
                            ->required(),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::trColumn('name', 'Nama'),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state): string => rupiah($state))
                    ->sortable(),
                TextColumn::make('unit')
                    ->label('Satuan')
                    ->formatStateUsing(fn (?string $state): string => self::UNITS[$state] ?? (string) $state)
                    ->badge(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
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

    public static function getPages(): array
    {
        return [
            'index' => ListAddons::route('/'),
            'create' => CreateAddon::route('/create'),
            'edit' => EditAddon::route('/{record}/edit'),
        ];
    }
}
