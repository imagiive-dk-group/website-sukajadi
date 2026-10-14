<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Support\Fields;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Berita';

    protected static ?string $modelLabel = 'berita';

    protected static ?string $pluralModelLabel = 'berita';

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        return $record ? localized($record->title, 'id') : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Isi berita')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::bilingual(fn (string $locale): array => [
                            Fields::slugSource(
                                TextInput::make("title.$locale")
                                    ->label('Judul')
                                    ->required($locale === 'id')
                                    ->maxLength(255),
                                $locale === 'id' ? 'slug' : 'slug_en',
                                'title.en',
                            ),
                            Textarea::make("excerpt.$locale")
                                ->label('Ringkasan')
                                ->rows(2)
                                ->maxLength(500),
                            Fields::richEditor("body.$locale")
                                ->label('Isi berita'),
                        ]),
                        ...Fields::slugFields(),
                    ]),

                Section::make('Foto sampul')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Fields::image('cover_path', 'posts', 'Foto sampul'),
                        Fields::placeholderLabel(),
                    ]),

                Section::make('Penerbitan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Terbitkan')
                            ->inline(false),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal terbit')
                            ->helperText('Kosongkan untuk terbit sekarang. Isi tanggal mendatang untuk menjadwalkan.')
                            ->seconds(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Fields::imageColumn(),
                Fields::trColumn('title', 'Judul')
                    ->wrap(),
                ToggleColumn::make('is_published')
                    ->label('Terbit'),
                TextColumn::make('published_at')
                    ->label('Tanggal terbit')
                    ->dateTime('j M Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Terbit'),
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
