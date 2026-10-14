<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\BookingCalendar;
use App\Filament\Pages\Reports;
use App\Filament\Pages\Settings;
use App\Filament\Resources\Addons\AddonResource;
use App\Filament\Resources\Albums\AlbumResource;
use App\Filament\Resources\Attractions\AttractionResource;
use App\Filament\Resources\BlockedDates\BlockedDateResource;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Messages\MessageResource;
use App\Filament\Resources\Officials\OfficialResource;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\ProfileSections\ProfileSectionResource;
use App\Filament\Resources\ProfileStats\ProfileStatResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Package;
use App\Filament\Widgets\BookingsChart;
use App\Filament\Widgets\BookingStats;
use App\Filament\Widgets\UpcomingVisits;
use Livewire\Livewire;

class PageAccessTest extends AdminTestCase
{
    /** @var array<class-string> */
    protected const RESOURCES = [
        AddonResource::class,
        AlbumResource::class,
        AttractionResource::class,
        BlockedDateResource::class,
        BookingResource::class,
        FaqResource::class,
        MessageResource::class,
        OfficialResource::class,
        PackageResource::class,
        PostResource::class,
        ProfileSectionResource::class,
        ProfileStatResource::class,
        TestimonialResource::class,
        UmkmResource::class,
        UserResource::class,
    ];

    protected function seedSampleData(): void
    {
        $this->seed();

        $package = Package::query()->with('tiers')->firstOrFail();
        $tier = $package->tiers->first();

        Booking::create([
            'code' => Booking::generateCode(),
            'package_id' => $package->id,
            'package_tier_id' => $tier->id,
            'package_name' => $package->tr('name', 'id'),
            'visit_date' => today()->addDays(5),
            'pax' => 20,
            'customer_name' => 'Ibu Sari',
            'phone' => '081234567890',
            'organization' => 'SDN Sukajadi 1',
            'group_type' => 'school',
            'price_per_pax' => $tier->price,
            'addons' => [['id' => 1, 'name' => ['id' => 'Dokumentasi', 'en' => 'Documentation'], 'unit' => 'per_group', 'price' => 150000, 'subtotal' => 150000]],
            'subtotal' => $tier->price * 20,
            'addons_total' => 150000,
            'total' => $tier->price * 20 + 150000,
        ]);

        Message::create(['name' => 'Budi', 'contact' => '0857 1234 5678', 'message' => 'Halo, mau tanya paket.']);
        BlockedDate::create(['date' => today()->addDays(7), 'reason' => 'Acara desa']);
    }

    public function test_super_admin_can_open_every_resource_page(): void
    {
        $this->seedSampleData();
        $user = $this->actingAsSuperAdmin();

        foreach (self::RESOURCES as $resource) {
            $this->get($resource::getUrl('index'))->assertOk();

            if ($resource::hasPage('create')) {
                $this->get($resource::getUrl('create'))->assertOk();
            }

            $record = $resource === UserResource::class ? $user : $resource::getModel()::query()->firstOrFail();

            foreach (['view', 'edit'] as $page) {
                if ($resource::hasPage($page)) {
                    $this->get($resource::getUrl($page, ['record' => $record]))->assertOk();
                }
            }
        }
    }

    public function test_super_admin_can_open_custom_pages_and_dashboard(): void
    {
        $this->seedSampleData();
        $this->actingAsSuperAdmin();

        $this->get('/admin')->assertOk();
        Livewire::test(BookingStats::class)->assertOk()->assertSee('Booking baru');
        Livewire::test(UpcomingVisits::class)->assertOk()->assertSee('Kunjungan 14 hari ke depan')->assertSee('Ibu Sari');
        Livewire::test(BookingsChart::class)->assertOk();
        $this->get(Settings::getUrl())->assertOk()->assertSee('Pengaturan Website');
        $this->get(BookingCalendar::getUrl())->assertOk()->assertSee('Kalender Booking');
        $this->get(BookingCalendar::getUrl().'?bulan='.today()->addDays(5)->format('Y-m'))->assertOk();
        $this->get(Reports::getUrl())->assertOk()->assertSee('Ekspor CSV (Excel)');
    }

    public function test_admin_role_cannot_access_users_and_settings(): void
    {
        $this->seedSampleData();
        $this->actingAs($this->admin());

        $this->get(UserResource::getUrl('index'))->assertForbidden();
        $this->get(UserResource::getUrl('create'))->assertForbidden();
        $this->get(Settings::getUrl())->assertForbidden();

        // Menu lain tetap bisa dibuka oleh admin biasa.
        $this->get(BookingResource::getUrl('index'))->assertOk();
        $this->get(PackageResource::getUrl('index'))->assertOk();
        $this->get('/admin')->assertOk()->assertDontSee('Pengguna Admin');
    }

    public function test_inactive_user_cannot_access_panel(): void
    {
        $user = $this->superAdmin();
        $user->update(['is_active' => false]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}
