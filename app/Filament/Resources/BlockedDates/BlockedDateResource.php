<?php

namespace App\Filament\Resources\BlockedDates;

use App\Filament\Resources\BlockedDates\Pages\CreateBlockedDate;
use App\Filament\Resources\BlockedDates\Pages\EditBlockedDate;
use App\Filament\Resources\BlockedDates\Pages\ListBlockedDates;
use App\Models\BlockedDate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class BlockedDateResource extends Resource
{
    protected static ?string $model = BlockedDate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static string|UnitEnum|null $navigationGroup = 'Booking';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tanggal Ditutup';

    protected static ?string $modelLabel = 'tanggal ditutup';

    protected static ?string $pluralModelLabel = 'tanggal ditutup';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record?->date?->translatedFormat('j F Y');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tutup tanggal untuk booking')
                    ->description('Pengunjung tidak bisa memilih tanggal ini di formulir booking.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('date')
                            ->label('Tanggal')
                            ->required(),
                        Select::make('package_id')
                            ->label('Paket')
                            ->relationship('package', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => (string) localized($record->name, 'id'))
                            ->placeholder('Semua paket')
                            ->helperText('Kosongkan untuk menutup semua paket pada tanggal ini.')
                            ->preload(),
                        TextInput::make('reason')
                            ->label('Alasan')
                            ->placeholder('Misal: Hari raya, acara desa, perawatan lokasi')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('l, j F Y')
                    ->sortable(),
                TextColumn::make('package.name')
                    ->label('Paket')
                    ->getStateUsing(fn (BlockedDate $record): string => $record->package ? (string) localized($record->package->name, 'id') : 'Semua paket'),
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->placeholder('-')
                    ->wrap(),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Hanya yang akan datang')
                    ->toggle()
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', '>=', today())),
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
            'index' => ListBlockedDates::route('/'),
            'create' => CreateBlockedDate::route('/create'),
            'edit' => EditBlockedDate::route('/{record}/edit'),
        ];
    }
}
