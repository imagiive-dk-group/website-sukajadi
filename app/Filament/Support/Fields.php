<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Komponen form/tabel yang dipakai berulang di panel admin.
 */
class Fields
{
    public const LOCALES = ['id' => 'Indonesia', 'en' => 'English'];

    /**
     * Tab "Indonesia" & "English" untuk kolom dua bahasa ({"id": .., "en": ..}).
     * $fields menerima kode bahasa dan mengembalikan daftar komponen, misal TextInput::make("name.$locale").
     *
     * @param  Closure(string): array  $fields
     */
    public static function bilingual(Closure $fields, string $key = 'translations', ?string $label = null): Tabs
    {
        return Tabs::make($label ?? 'Teks dua bahasa')
            ->key($key)
            ->tabs([
                Tab::make('Indonesia')
                    ->key($key.'-id')
                    ->schema($fields('id')),
                Tab::make('English')
                    ->key($key.'-en')
                    ->schema([
                        Text::make('Opsional. Bila dikosongkan, halaman versi English memakai teks Bahasa Indonesia.')
                            ->color('gray'),
                        ...$fields('en'),
                    ]),
            ])
            ->columnSpanFull();
    }

    public static function richEditor(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->fileAttachmentsDisk('uploads')
            ->fileAttachmentsDirectory('editor')
            ->fileAttachmentsVisibility('public')
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'link'],
                ['h2', 'h3', 'bulletList', 'orderedList', 'blockquote'],
                ['attachFiles'],
                ['undo', 'redo'],
            ]);
    }

    public static function image(string $name, string $directory, string $label = 'Foto'): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->disk('uploads')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->imageEditor()
            ->maxSize(10240)
            ->helperText('Format JPG/PNG/WebP, maks. 10 MB. Versi WebP ukuran kecil/sedang/besar dibuat otomatis.');
    }

    public static function placeholderLabel(): TextInput
    {
        return TextInput::make('placeholder_label')
            ->label('Keterangan foto yang dibutuhkan')
            ->helperText('Keterangan foto yang dibutuhkan; tampil di placeholder selama foto belum diunggah')
            ->maxLength(255);
    }

    /**
     * Tambahkan pengisian slug otomatis saat membuat data baru.
     * Untuk sumber Indonesia ($target = 'slug') sekaligus mengisi slug_en bila nama English kosong.
     */
    public static function slugSource(TextInput $input, string $target = 'slug', ?string $englishSource = null): TextInput
    {
        return $input
            ->live(onBlur: true)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) use ($target, $englishSource): void {
                if ($operation !== 'create') {
                    return;
                }

                if ($target === 'slug_en' && blank($state)) {
                    $state = $get('slug');
                }

                $set($target, Str::slug((string) $state));

                if ($target === 'slug' && $englishSource && blank($get($englishSource))) {
                    $set('slug_en', Str::slug((string) $state));
                }
            });
    }

    /** @return array<TextInput> */
    public static function slugFields(): array
    {
        return [
            TextInput::make('slug')
                ->label('Slug (alamat URL Indonesia)')
                ->helperText('Terisi otomatis dari judul/nama. Hanya huruf kecil, angka, dan tanda hubung.')
                ->required()
                ->maxLength(255)
                ->alphaDash()
                ->unique(ignoreRecord: true),
            TextInput::make('slug_en')
                ->label('Slug (alamat URL English)')
                ->helperText('Kosongkan untuk memakai slug Indonesia.')
                ->maxLength(255)
                ->alphaDash()
                ->unique(ignoreRecord: true)
                ->dehydrateStateUsing(fn (?string $state, Get $get): ?string => filled($state) ? $state : $get('slug')),
        ];
    }

    /** Kolom tabel untuk nilai dua bahasa (menampilkan versi Indonesia). */
    public static function trColumn(string $name, string $label): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->getStateUsing(fn (Model $record): ?string => localized($record->getAttribute($name), 'id'))
            ->searchable(query: fn (Builder $query, string $search): Builder => $query->where($name, 'like', '%'.$search.'%'))
            ->sortable();
    }

    public static function imageColumn(string $name = 'cover_path', string $label = 'Foto'): ImageColumn
    {
        return ImageColumn::make($name)
            ->label($label)
            ->disk('uploads')
            ->visibility('public')
            ->imageHeight(48)
            ->checkFileExistence(false);
    }

    /**
     * Opsi Select dari daftar di config/sukajadi.php dengan label dari lang/id/site.php.
     *
     * @return array<string, string>
     */
    public static function options(string $configKey, string $langPrefix): array
    {
        return collect(config('sukajadi.'.$configKey, []))
            ->mapWithKeys(fn (string $key): array => [$key => __($langPrefix.'.'.$key)])
            ->all();
    }

    /** @return array<string, string> */
    public static function faqCategories(): array
    {
        return collect(['booking', 'pembayaran', 'kunjungan', 'lokasi', 'umum'])
            ->mapWithKeys(fn (string $key): array => [$key => __('site.faq.categories.'.$key)])
            ->all();
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'new' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }
}
