<?php

namespace App\Filament\Resources\Officials;

use App\Filament\Resources\Officials\Pages\CreateOfficial;
use App\Filament\Resources\Officials\Pages\EditOfficial;
use App\Filament\Resources\Officials\Pages\ListOfficials;
use App\Filament\Support\Fields;
use App\Models\Official;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OfficialResource extends Resource
{
    protected static ?string $model = Official::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Desa';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Perangkat Desa';

    protected static ?string $modelLabel = 'perangkat desa';

    protected static ?string $pluralModelLabel = 'perangkat desa';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Perangkat desa')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        TextInput::make('position.id')
                            ->label('Jabatan (Indonesia)')
                            ->placeholder('Misal: Kepala Desa')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('position.en')
                            ->label('Jabatan (English)')
                            ->placeholder('e.g. Village Head')
                            ->maxLength(255),
                        Fields::image('photo_path', 'officials', 'Foto'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Fields::imageColumn('photo_path')->circular(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Fields::trColumn('position', 'Jabatan'),
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
            'index' => ListOfficials::route('/'),
            'create' => CreateOfficial::route('/create'),
            'edit' => EditOfficial::route('/{record}/edit'),
        ];
    }
}
