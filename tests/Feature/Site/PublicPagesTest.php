<?php

namespace Tests\Feature\Site;

use App\Models\Album;
use App\Models\Attraction;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed per tes: RefreshDatabase hanya men-seed saat migrasi pertama,
        // yang bisa saja dipicu oleh kelas tes lain tanpa seeder.
        $this->seed();
        $this->withoutVite();
    }

    public static function staticRoutes(): array
    {
        return collect(['home', 'profile', 'about', 'packages.index', 'custom', 'attractions.index', 'umkm.index', 'products', 'gallery.index', 'news.index', 'faq', 'contact', 'reviews', 'booking.create', 'booking.check', 'partners.index', 'team'])
            ->flatMap(fn ($name) => ["id $name" => ['id', $name], "en $name" => ['en', $name]])
            ->all();
    }

    #[DataProvider('staticRoutes')]
    public function test_static_pages_render_in_both_languages(string $locale, string $name): void
    {
        $this->get(route("$locale.$name"))
            ->assertOk()
            ->assertSee('<html lang="'.$locale.'">', false)
            ->assertSee('hreflang="en"', false);
    }

    public function test_detail_pages_render_with_localized_slugs(): void
    {
        foreach ([Package::first(), Attraction::first(), Umkm::first(), Album::first(), Post::first(), Partner::first()] as $model) {
            $this->get($model->url('id'))->assertOk();
            $this->get($model->url('en'))->assertOk();
        }

        $package = Package::where('slug', 'budidaya-kelinci')->first();
        $this->get('/paket/budidaya-kelinci')->assertOk()->assertSee('Paket Edukasi Budidaya Kelinci')->assertSee('Rp125.000');
        $this->get('/en/packages/rabbit-farming')->assertOk()->assertSee('Rabbit Farming Education Package')
            ->assertSee(route('id.packages.show', $package->slug), false);
    }

    public function test_english_falls_back_to_indonesian_when_translation_is_empty(): void
    {
        $package = Package::first();
        $package->update(['summary' => ['id' => 'Ringkasan khusus Indonesia', 'en' => '']]);

        $this->get($package->url('en'))->assertOk()->assertSee('Ringkasan khusus Indonesia');
    }

    public function test_inactive_content_is_hidden(): void
    {
        $package = Package::first();
        $package->update(['is_active' => false]);

        $this->get($package->url('id'))->assertNotFound();
        $this->get(route('id.packages.index'))->assertDontSee($package->tr('name'));
    }

    public function test_umkm_contact_respects_owner_permission(): void
    {
        $umkm = Umkm::first();
        $umkm->update(['whatsapp' => '0812-1111-2222', 'show_contact' => false]);

        $this->get($umkm->url())->assertOk()->assertDontSee('0812-1111-2222')->assertDontSee('n=6281211112222', false)->assertSee('/wa?c=umkm', false);

        $umkm->update(['show_contact' => true]);
        $this->get($umkm->url())->assertOk()->assertSee('0812-1111-2222')->assertSee('n=6281211112222', false);
    }

    public function test_profile_page_shows_sources_and_verification_badges(): void
    {
        $this->get(route('id.profile'))->assertOk()
            ->assertSee('32.01.31.2008')
            ->assertSee('Perlu verifikasi')
            ->assertSee('Kecamatan Tamansari Dalam Angka 2025');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post(route('id.contact.send'), ['name' => 'Budi', 'contact' => '08123456789', 'message' => 'Halo, mau tanya paket.'])
            ->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('messages', ['name' => 'Budi']);
    }

    public function test_honeypot_blocks_spam(): void
    {
        $this->post(route('id.contact.send'), ['name' => 'Bot', 'contact' => 'x', 'message' => 'spam spam', 'website' => 'http://spam'])
            ->assertSessionHasErrors('website');

        $this->assertDatabaseMissing('messages', ['name' => 'Bot']);
    }

    public function test_reviews_are_moderated(): void
    {
        $this->post(route('en.reviews.store'), ['name' => 'Anna', 'rating' => 5, 'content' => 'Wonderful village trip!'])
            ->assertRedirect()->assertSessionHas('status');

        $review = Testimonial::where('name', 'Anna')->first();
        $this->assertFalse($review->is_approved);
        $this->assertSame('en', $review->locale);
        $this->get(route('id.reviews'))->assertDontSee('Wonderful village trip!');
    }

    public function test_sitemap_lists_both_languages(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(url('/paket/budidaya-kelinci'), false)
            ->assertSee(url('/en/packages/rabbit-farming'), false);
    }

    public function test_homepage_tells_the_village_story_before_packages(): void
    {
        $html = $this->get(route('id.home'))->assertOk()->getContent();

        $story = strpos($html, 'id="cerita"');
        $packages = strpos($html, 'Paket kunjungan yang sudah kami siapkan');
        $this->assertNotFalse($story);
        $this->assertNotFalse($packages);
        $this->assertLessThan($packages, $story);
        $this->assertStringContainsString('Cerita dari Sukajadi', $html);
        $this->assertStringContainsString('Highland Tamansari', $html);
    }

    public function test_homepage_uses_hero_video_when_uploaded(): void
    {
        Setting::put('hero_video', 'settings/hero.mp4');

        $this->get(route('id.home'))->assertOk()->assertSee('<video', false)->assertSee('/uploads/settings/hero.mp4', false);
    }

    public function test_profile_has_no_government_officials_and_team_page_lists_activists(): void
    {
        $this->get(route('id.profile'))->assertOk()->assertDontSee('Kepala desa')->assertDontSee('Kepala Desa');
        $this->get(route('id.team'))->assertOk()->assertSee('Ketua Penggiat Desa Wisata');
    }

    public function test_whatsapp_clicks_are_tracked_and_redirect_to_wa_me(): void
    {
        $this->get(route('wa', ['c' => 'partner', 'i' => 1, 't' => 'Halo']))
            ->assertRedirect('https://wa.me/6285775194575?text=Halo');

        $this->get(route('wa', ['c' => 'evil', 'n' => 'https://evil.example']))
            ->assertRedirect('https://wa.me/6285775194575');

        $this->assertDatabaseHas('wa_clicks', ['context_type' => 'partner', 'context_id' => 1]);
        $this->assertDatabaseHas('wa_clicks', ['context_type' => 'general']);
    }

    public function test_contact_message_creates_lead(): void
    {
        $this->post(route('id.contact.send'), ['name' => 'Rina', 'contact' => 'rina@example.com', 'message' => 'Mau tanya vila untuk 10 orang.']);

        $this->assertDatabaseHas('leads', ['name' => 'Rina', 'source' => 'contact', 'email' => 'rina@example.com', 'status' => 'new']);
    }

    public function test_placeholder_svg_is_served(): void
    {
        $this->get('/placeholder.svg?label=Curug%20Aman')->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertSee('Curug Aman');
    }
}
