<?php

namespace App\Filament\Pages;

use App\Filament\Support\Fields;
use App\Models\Setting;
use App\Models\User;
use App\Support\Images;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Pengaturan website yang disimpan di tabel settings (key → value JSON).
 *
 * @property-read Schema $form
 */
class Settings extends Page
{
    /** Kolom teks dua bahasa: ['id' => .., 'en' => ..]. */
    public const BILINGUAL = [
        'tagline', 'motto', 'hero_title', 'hero_subtitle', 'about_intro', 'about_body', 'address', 'opening_hours',
    ];

    /** Kolom teks biasa. */
    public const STRINGS = [
        'site_name', 'hero_image', 'whatsapp', 'instagram', 'email', 'maps_embed_url', 'maps_link',
        'notification_email', 'ga_measurement_id',
    ];

    /** Kolom angka (null = tidak diisi). */
    public const INTEGERS = [
        'min_lead_days', 'max_advance_days', 'daily_capacity', 'custom_min_pax', 'custom_base_price',
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?string $slug = 'pengaturan';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    public function mount(): void
    {
        $values = [];

        foreach ([...self::BILINGUAL, ...self::STRINGS, ...self::INTEGERS] as $key) {
            $value = Setting::get($key);

            if (in_array($key, self::BILINGUAL, true)) {
                $value = [
                    'id' => is_array($value) ? ($value['id'] ?? null) : $value,
                    'en' => is_array($value) ? ($value['en'] ?? null) : null,
                ];
            }

            $values[$key] = $value;
        }

        $this->form->fill($values);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Identitas & beranda')
                    ->description('Nama, tagline, dan teks utama di halaman depan.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nama website')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('hero_image')
                            ->label('Foto hero (beranda)')
                            ->disk('uploads')
                            ->directory('settings')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->maxSize(10240)
                            ->helperText('Foto lanskap besar, minimal lebar 1920px. Versi WebP dibuat otomatis.'),
                        Fields::bilingual(fn (string $locale): array => [
                            TextInput::make("tagline.$locale")->label('Tagline')->required($locale === 'id')->maxLength(255),
                            TextInput::make("motto.$locale")->label('Motto')->maxLength(255),
                            TextInput::make("hero_title.$locale")->label('Judul hero')->required($locale === 'id')->maxLength(255),
                            Textarea::make("hero_subtitle.$locale")->label('Subjudul hero')->rows(2),
                            Textarea::make("about_intro.$locale")->label('Pengantar "Tentang Desa"')->rows(3),
                            Fields::richEditor("about_body.$locale")->label('Isi "Tentang Desa"'),
                        ], 'identity-translations'),
                    ]),

                Section::make('Kontak')
                    ->description('Dipakai di header, footer, tombol WhatsApp, dan halaman Kontak.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('whatsapp')
                            ->label('Nomor WhatsApp admin')
                            ->helperText('Tujuan tombol WhatsApp & pesan booking. Format bebas, misal +62 857-7519-4575.')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->maxLength(100),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('maps_link')
                            ->label('Link Google Maps')
                            ->url()
                            ->maxLength(1000),
                        TextInput::make('maps_embed_url')
                            ->label('URL embed Google Maps')
                            ->helperText('Dari Google Maps → Bagikan → Sematkan peta, salin isi atribut src.')
                            ->url()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Fields::bilingual(fn (string $locale): array => [
                            TextInput::make("address.$locale")->label('Alamat')->required($locale === 'id')->maxLength(255),
                            TextInput::make("opening_hours.$locale")->label('Jam operasional')->maxLength(255),
                        ], 'contact-translations'),
                    ]),

                Section::make('Booking')
                    ->description('Aturan formulir booking di website.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('min_lead_days')
                            ->label('Minimal hari sebelum kunjungan (H-x)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->suffix('hari'),
                        TextInput::make('max_advance_days')
                            ->label('Maksimal booking ke depan')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->suffix('hari'),
                        TextInput::make('daily_capacity')
                            ->label('Kuota harian (semua paket)')
                            ->helperText('Kuota pax per hari untuk semua paket; kosongkan = tidak dibatasi')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('pax'),
                        TextInput::make('notification_email')
                            ->label('Email notifikasi tambahan')
                            ->helperText('Email tambahan penerima notifikasi booking')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('custom_min_pax')
                            ->label('Minimal peserta paket custom')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->suffix('pax'),
                        TextInput::make('custom_base_price')
                            ->label('Biaya dasar paket custom (per pax)')
                            ->helperText('Pemandu, transportasi area desa, snack. Ditambah harga tiap aktivitas yang dipilih.')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefix('Rp'),
                    ]),

                Section::make('Lainnya')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('ga_measurement_id')
                            ->label('Google Analytics 4 Measurement ID (G-XXXX)')
                            ->placeholder('G-XXXXXXXXXX')
                            ->regex('/^G-[A-Z0-9]+$/')
                            ->validationMessages(['regex' => 'Format ID harus seperti G-XXXXXXXXXX.'])
                            ->maxLength(50),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan pengaturan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $oldHero = Setting::get('hero_image');

        foreach (self::BILINGUAL as $key) {
            $value = $data[$key] ?? [];
            Setting::put($key, ['id' => $value['id'] ?? null, 'en' => $value['en'] ?? null]);
        }

        foreach (self::STRINGS as $key) {
            Setting::put($key, filled($data[$key] ?? null) ? (string) $data[$key] : null);
        }

        foreach (self::INTEGERS as $key) {
            Setting::put($key, filled($data[$key] ?? null) ? (int) $data[$key] : null);
        }

        // Foto hero: buat varian WebP & hapus file lama bila diganti.
        $hero = $data['hero_image'] ?? null;
        if ($hero !== $oldHero) {
            if (filled($oldHero)) {
                Images::delete($oldHero);
            }
            if (filled($hero)) {
                Images::generate($hero);
            }
        }

        Notification::make()
            ->title('Pengaturan tersimpan')
            ->success()
            ->send();
    }
}
