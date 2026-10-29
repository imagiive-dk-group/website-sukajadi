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
use Illuminate\Support\Facades\Storage;
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
        'story_title', 'story_body', 'managed_by', 'supporter_text',
    ];

    /** Kolom teks biasa. */
    public const STRINGS = [
        'site_name', 'hero_image', 'hero_video', 'whatsapp', 'instagram', 'tiktok', 'email', 'maps_embed_url', 'maps_link',
        'notification_email', 'ga_measurement_id', 'supporter_name', 'supporter_logo', 'supporter_url', 'sponsor_name',
    ];

    /** Kolom angka (null = tidak diisi). */
    public const INTEGERS = [
        'min_lead_days', 'partner_min_lead_days', 'max_advance_days', 'daily_capacity', 'custom_min_pax', 'custom_base_price',
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

        $values['hero_slides'] = array_values(array_filter((array) (Setting::get('hero_slides') ?? []), 'filled'));

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

                Section::make('Beranda')
                    ->description('Video/slideshow latar, cerita desa, dan keterangan pengelola di halaman depan.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        FileUpload::make('hero_video')
                            ->label('Video latar beranda')
                            ->disk('uploads')
                            ->directory('settings')
                            ->visibility('public')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->maxSize(30720)
                            ->helperText('Video latar beranda (MP4, 10–30 detik, tanpa suara, maks. 30 MB). Kosongkan untuk memakai slideshow foto.'),
                        FileUpload::make('hero_slides')
                            ->label('Slideshow foto beranda')
                            ->disk('uploads')
                            ->directory('settings')
                            ->visibility('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->maxFiles(6)
                            ->maxSize(10240)
                            ->maxParallelUploads(3)
                            ->helperText('Maks. 6 foto lanskap (min. lebar 1920px), urutan bisa diatur. Dipakai bila video kosong; bila slideshow juga kosong, dipakai foto hero.'),
                        Fields::bilingual(fn (string $locale): array => [
                            TextInput::make("story_title.$locale")->label('Judul cerita desa')->maxLength(255),
                            Fields::richEditor("story_body.$locale")->label('Isi cerita desa'),
                            TextInput::make("managed_by.$locale")
                                ->label('Keterangan pengelola')
                                ->placeholder($locale === 'id' ? 'Misal: Dikelola oleh penggiat Desa Wisata Sukajadi' : 'e.g. Run by the Sukajadi tourism village team')
                                ->maxLength(255),
                        ], 'home-translations'),
                    ]),

                Section::make('Pendamping program')
                    ->description('Lembaga pendamping (Yayasan LEADS) dan pendukung dana CSR. Tampil di footer dan halaman Profil Desa; kosongkan nama lembaga untuk menyembunyikan.')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextInput::make('supporter_name')
                            ->label('Nama lembaga pendamping')
                            ->placeholder('Yayasan LEADS')
                            ->maxLength(255),
                        TextInput::make('sponsor_name')
                            ->label('Pendukung dana (CSR)')
                            ->placeholder('PT RATU')
                            ->maxLength(255),
                        FileUpload::make('supporter_logo')
                            ->label('Logo lembaga pendamping')
                            ->disk('uploads')
                            ->directory('settings')
                            ->visibility('public')
                            ->image()
                            ->maxSize(2048)
                            ->helperText('PNG/SVG/WebP dengan latar transparan, maks. 2 MB. Ditampilkan apa adanya (tidak diubah ke WebP).'),
                        TextInput::make('supporter_url')
                            ->label('Website lembaga pendamping')
                            ->url()
                            ->maxLength(1000),
                        Fields::bilingual(fn (string $locale): array => [
                            Textarea::make("supporter_text.$locale")
                                ->label('Keterangan program')
                                ->placeholder($locale === 'id'
                                    ? 'Misal: Program Sukajadi Mandiri didampingi Yayasan LEADS dengan dukungan dana CSR PT RATU.'
                                    : 'e.g. The Sukajadi Mandiri program is supported by the LEADS Foundation, funded by PT RATU CSR.')
                                ->rows(2)
                                ->maxLength(500),
                        ], 'supporter-translations'),
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
                        TextInput::make('tiktok')
                            ->label('TikTok')
                            ->prefix('@')
                            ->helperText('Kosongkan bila belum punya akun; ikon TikTok otomatis disembunyikan.')
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
                        TextInput::make('partner_min_lead_days')
                            ->label('Batas minimal hari booking untuk mitra')
                            ->helperText('Minimal hari sebelum tanggal kunjungan/check-in untuk booking mitra bisnis (H-x).')
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
        $oldVideo = Setting::get('hero_video');
        $oldLogo = Setting::get('supporter_logo');
        $oldSlides = array_values(array_filter((array) (Setting::get('hero_slides') ?? []), 'filled'));

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

        // Video latar: hapus file lama bila diganti/dikosongkan.
        $video = $data['hero_video'] ?? null;
        if (filled($oldVideo) && $video !== $oldVideo) {
            Storage::disk('uploads')->delete($oldVideo);
        }

        // Logo pendamping: hapus file lama bila diganti/dikosongkan.
        if (filled($oldLogo) && ($data['supporter_logo'] ?? null) !== $oldLogo) {
            Storage::disk('uploads')->delete($oldLogo);
        }

        // Slideshow: simpan sebagai daftar path, buat varian WebP untuk foto baru, hapus foto yang dibuang.
        $slides = array_values(array_filter((array) ($data['hero_slides'] ?? []), 'filled'));
        Setting::put('hero_slides', $slides ?: null);

        foreach (array_diff($slides, $oldSlides) as $path) {
            Images::generate($path);
        }
        foreach (array_diff($oldSlides, $slides) as $path) {
            Images::delete($path);
        }

        Notification::make()
            ->title('Pengaturan tersimpan')
            ->success()
            ->send();
    }
}
