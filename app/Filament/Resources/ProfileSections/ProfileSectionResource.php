<?php

namespace App\Filament\Resources\ProfileSections;

use App\Filament\Resources\ProfileSections\Pages\CreateProfileSection;
use App\Filament\Resources\ProfileSections\Pages\EditProfileSection;
use App\Filament\Resources\ProfileSections\Pages\ListProfileSections;
use App\Filament\Support\Fields;
use App\Models\ProfileSection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class ProfileSectionResource extends Resource
{
    protected static ?string $model = ProfileSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Desa';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Bagian Profil';

    protected static ?string $modelLabel = 'bagian profil';

    protected static ?string $pluralModelLabel = 'bagian profil';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->title, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bagian halaman Profil Desa')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('key')
                            ->label('Kode bagian')
                            ->helperText('Kode unik yang dipakai halaman website, misal: sejarah, visi_misi. Jangan diubah bila sudah dipakai.')
                            ->required()
                            ->maxLength(100)
                            ->alphaDash()
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Fields::bilingual(fn (string $locale): array => [
                            TextInput::make("title.$locale")
                                ->label('Judul')
                                ->required($locale === 'id')
                                ->maxLength(255),
                            Fields::richEditor("body.$locale")
                                ->label('Isi'),
                        ]),
                        Toggle::make('is_active')
                            ->label('Aktif')
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
                Fields::trColumn('title', 'Judul'),
                TextColumn::make('key')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),
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
            'index' => ListProfileSections::route('/'),
            'create' => CreateProfileSection::route('/create'),
            'edit' => EditProfileSection::route('/{record}/edit'),
        ];
    }
}
