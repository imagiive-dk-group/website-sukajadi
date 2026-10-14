<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Support\Fields;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Ulasan';

    protected static ?string $modelLabel = 'ulasan';

    protected static ?string $pluralModelLabel = 'ulasan';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $count = Testimonial::query()->where('is_approved', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Menunggu persetujuan';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ulasan pengunjung')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('organization')
                            ->label('Asal / instansi')
                            ->maxLength(255),
                        Select::make('rating')
                            ->label('Rating')
                            ->options([5 => '★★★★★ (5)', 4 => '★★★★ (4)', 3 => '★★★ (3)', 2 => '★★ (2)', 1 => '★ (1)'])
                            ->default(5)
                            ->required(),
                        Select::make('locale')
                            ->label('Bahasa ulasan')
                            ->options(Fields::LOCALES)
                            ->default('id')
                            ->required(),
                        Textarea::make('content')
                            ->label('Isi ulasan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Fields::image('photo_path', 'testimonials', 'Foto (opsional)'),
                        Section::make()
                            ->schema([
                                Toggle::make('is_approved')->label('Disetujui (tampil di website)'),
                                Toggle::make('is_featured')->label('Unggulan (tampil di beranda)'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn (Testimonial $record): ?string => $record->organization)
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state): string => str_repeat('★', (int) $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Ulasan')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('locale')
                    ->label('Bahasa')
                    ->badge(),
                IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('j M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Status')
                    ->trueLabel('Disetujui')
                    ->falseLabel('Menunggu persetujuan'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Testimonial $record): bool => ! $record->is_approved)
                    ->action(function (Testimonial $record): void {
                        $record->update(['is_approved' => true]);

                        Notification::make()->title('Ulasan disetujui')->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Setujui yang dipilih')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
