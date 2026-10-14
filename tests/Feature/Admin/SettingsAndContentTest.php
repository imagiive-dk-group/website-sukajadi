<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Settings;
use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Albums\Pages\EditAlbum;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Messages\Pages\ViewMessage;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Umkms\Pages\CreateUmkm;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Album;
use App\Models\Faq;
use App\Models\Message;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Umkm;
use Database\Seeders\SettingSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class SettingsAndContentTest extends AdminTestCase
{
    public function test_settings_page_saves_bilingual_and_numeric_values(): void
    {
        $this->seed(SettingSeeder::class);
        $this->actingAsSuperAdmin();

        Livewire::test(Settings::class)
            ->assertSchemaStateSet(['tagline.id' => 'Belajar • Berwisata • Berdaya', 'min_lead_days' => 3])
            ->fillForm([
                'tagline' => ['id' => 'Tagline baru', 'en' => 'New tagline'],
                'whatsapp' => '0811-2222-3333',
                'daily_capacity' => '150',
                'custom_base_price' => '40000',
                'ga_measurement_id' => 'G-ABC123',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['id' => 'Tagline baru', 'en' => 'New tagline'], Setting::get('tagline'));
        $this->assertSame('0811-2222-3333', Setting::get('whatsapp'));
        $this->assertSame(150, Setting::get('daily_capacity'));
        $this->assertSame(40000, Setting::get('custom_base_price'));
        $this->assertSame('G-ABC123', Setting::get('ga_measurement_id'));
        // Nilai lain tidak berubah.
        $this->assertSame('Desa Wisata Sukajadi', Setting::get('site_name'));
        $this->assertSame(3, Setting::get('min_lead_days'));
        $this->assertStringContainsString('<p>', Setting::get('about_body')['id']);
        // Kosong = tidak dibatasi.
        Livewire::test(Settings::class)->fillForm(['daily_capacity' => null])->call('save')->assertHasNoFormErrors();
        $this->assertNull(Setting::get('daily_capacity'));
    }

    public function test_settings_rejects_invalid_ga_id(): void
    {
        $this->seed(SettingSeeder::class);
        $this->actingAsSuperAdmin();

        Livewire::test(Settings::class)
            ->fillForm(['ga_measurement_id' => 'UA-123'])
            ->call('save')
            ->assertHasFormErrors(['ga_measurement_id']);
    }

    public function test_faq_and_umkm_create_persist_bilingual_values(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(CreateFaq::class)
            ->fillForm([
                'question' => ['id' => 'Apakah ada parkir bus?', 'en' => 'Is there bus parking?'],
                'answer' => ['id' => 'Ada, di balai desa.', 'en' => null],
                'category' => 'lokasi',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $faq = Faq::query()->firstOrFail();
        $this->assertSame('Is there bus parking?', $faq->question['en']);
        $this->assertSame('Ada, di balai desa.', $faq->tr('answer', 'en'));

        Livewire::test(CreateUmkm::class)
            ->fillForm([
                'name' => 'Sari Pala Bu Iis',
                'category' => 'minuman',
                'summary' => ['id' => 'Minuman sari pala segar', 'en' => 'Fresh nutmeg juice'],
                'opening_hours' => ['id' => 'Setiap hari', 'en' => 'Daily'],
                'whatsapp' => '085700001111',
                'show_contact' => true,
            ])
            ->assertSchemaStateSet(['slug' => 'sari-pala-bu-iis'])
            ->call('create')
            ->assertHasNoFormErrors();

        $umkm = Umkm::query()->where('slug', 'sari-pala-bu-iis')->firstOrFail();
        $this->assertSame('Fresh nutmeg juice', $umkm->summary['en']);
        $this->assertTrue($umkm->hasPublicContact());
    }

    public function test_bulk_photo_upload_creates_one_photo_per_file(): void
    {
        Storage::fake('uploads');
        $this->actingAsSuperAdmin();

        $album = Album::create(['slug' => 'uji', 'slug_en' => 'test', 'title' => ['id' => 'Uji', 'en' => 'Test'], 'category' => 'alam']);

        Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $album, 'pageClass' => EditAlbum::class])
            ->callAction(TestAction::make('bulkUpload')->table(), [
                'files' => [
                    UploadedFile::fake()->image('a.jpg', 800, 600),
                    UploadedFile::fake()->image('b.jpg', 800, 600),
                    UploadedFile::fake()->image('c.jpg', 800, 600),
                ],
                'placeholder_label' => 'Kegiatan panen',
            ])
            ->assertHasNoFormErrors();

        $photos = $album->photos()->get();
        $this->assertCount(3, $photos);
        $this->assertSame([1, 2, 3], $photos->pluck('sort')->all());
        foreach ($photos as $photo) {
            $this->assertStringStartsWith('photos/albums/', $photo->path);
            Storage::disk('uploads')->assertExists($photo->path);
        }
    }

    public function test_viewing_message_marks_it_read(): void
    {
        $this->actingAsSuperAdmin();
        $message = Message::create(['name' => 'Rina', 'contact' => 'rina@example.com', 'message' => 'Halo']);

        Livewire::test(ViewMessage::class, ['record' => $message->getRouteKey()])
            ->assertActionHasUrl('reply', 'mailto:rina@example.com?subject='.rawurlencode('Balasan dari Desa Wisata Sukajadi'));

        $this->assertNotNull($message->refresh()->read_at);
    }

    public function test_approve_testimonial_action(): void
    {
        $this->actingAsSuperAdmin();
        $testimonial = Testimonial::create(['name' => 'Guru SD', 'content' => 'Seru sekali!', 'rating' => 5]);

        Livewire::test(ListTestimonials::class)
            ->callAction(TestAction::make('approve')->table($testimonial));

        $this->assertTrue($testimonial->refresh()->is_approved);
    }

    public function test_super_admin_cannot_deactivate_or_delete_self(): void
    {
        $user = $this->actingAsSuperAdmin();

        $this->assertFalse(UserResource::canDelete($user));

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertActionHidden('delete')
            ->fillForm(['is_active' => false, 'role' => 'admin'])
            ->call('save');

        $user->refresh();
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_super_admin_can_create_admin_user_with_hashed_password(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(\App\Filament\Resources\Users\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Admin Kedua',
                'email' => 'admin2@example.com',
                'password' => 'rahasia-123',
                'role' => 'admin',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = \App\Models\User::query()->where('email', 'admin2@example.com')->firstOrFail();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('rahasia-123', $created->password));
        $this->assertFalse($created->isSuperAdmin());
    }
}
