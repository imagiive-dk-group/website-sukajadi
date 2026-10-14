<?php

namespace App\Filament\Resources\ProfileStats;

use App\Filament\Resources\ProfileStats\Pages\CreateProfileStat;
use App\Filament\Resources\ProfileStats\Pages\EditProfileStat;
use App\Filament\Resources\ProfileStats\Pages\ListProfileStats;
use App\Filament\Support\Fields;
use App\Models\ProfileStat;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProfileStatResource extends Resource
{
    protected static ?string $model = ProfileStat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Profil Desa';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Data Profil';

    protected static ?string $modelLabel = 'data profil';

    protected static ?string $pluralModelLabel = 'data profil';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->label, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data resmi desa')
                    ->description('Setiap angka sebaiknya disertai sumber & tahun data.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('group')
                            ->label('Kelompok')
                            ->options(Fields::options('profile_groups', 'site.profile_groups'))
                            ->default('identitas')
                            ->required(),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Fields::bilingual(fn (string $locale): array => [
                            TextInput::make("label.$locale")
                                ->label('Label')
                                ->placeholder($locale === 'id' ? 'Misal: Luas wilayah' : 'e.g. Area')
                                ->required($locale === 'id')
                                ->maxLength(255),
                            TextInput::make("value.$locale")
                                ->label('Nilai')
                                ->placeholder($locale === 'id' ? 'Misal: 512 ha' : 'e.g. 512 ha')
                                ->required($locale === 'id')
                                ->maxLength(255),
                        ]),
                        TextInput::make('source')
                            ->label('Sumber data')
                            ->placeholder('Misal: BPS, Kecamatan Tamansari Dalam Angka')
                            ->maxLength(255),
                        TextInput::make('source_year')
                            ->label('Tahun data')
                            ->maxLength(20),
                        Toggle::make('needs_verification')
                            ->label('Perlu verifikasi')
                            ->helperText('Tampil penanda "perlu verifikasi" di website sampai data dipastikan.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->defaultGroup(
                Group::make('group')
                    ->label('Kelompok')
                    ->getTitleFromRecordUsing(fn (ProfileStat $record): string => __('site.profile_groups.'.$record->group))
            )
            ->columns([
                Fields::trColumn('label', 'Label'),
                Fields::trColumn('value', 'Nilai'),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->description(fn (ProfileStat $record): ?string => $record->source_year)
                    ->placeholder('-')
                    ->wrap(),
                IconColumn::make('needs_verification')
                    ->label('Perlu verifikasi')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('warning')
                    ->falseIcon(Heroicon::OutlinedCheckCircle)
                    ->falseColor('success'),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('Kelompok')
                    ->options(Fields::options('profile_groups', 'site.profile_groups')),
                TernaryFilter::make('needs_verification')->label('Perlu verifikasi'),
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
            'index' => ListProfileStats::route('/'),
            'create' => CreateProfileStat::route('/create'),
            'edit' => EditProfileStat::route('/{record}/edit'),
        ];
    }
}
