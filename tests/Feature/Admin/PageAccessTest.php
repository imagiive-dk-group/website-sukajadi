<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\BookingCalendar;
use App\Filament\Pages\Monitoring;
use App\Filament\Pages\Reports;
use App\Filament\Pages\Settings;
use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Addons\AddonResource;
use App\Filament\Resources\Albums\AlbumResource;
use App\Filament\Resources\Albums\Pages\EditAlbum;
use App\Filament\Resources\Attractions\AttractionResource;
use App\Filament\Resources\Attractions\Pages\EditAttraction;
use App\Filament\Resources\BlockedDates\BlockedDateResource;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Messages\MessageResource;
use App\Filament\Resources\Officials\OfficialResource;
use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\Partners\RelationManagers\OffersRelationManager;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\ProfileSections\ProfileSectionResource;
use App\Filament\Resources\ProfileStats\ProfileStatResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Filament\Resources\Umkms\Pages\EditUmkm;
use App\Filament\Resources\Umkms\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\BookingsChart;
use App\Filament\Widgets\BookingStats;
use App\Filament\Widgets\UpcomingVisits;
use App\Models\Album;
use App\Models\Attraction;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Umkm;
use App\Models\WaClick;
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
        LeadResource::class,
        MessageResource::class,
        OfficialResource::class,
        PackageResource::class,
        PartnerResource::class,
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

        $partner = Partner::query()->has('offers')->with('offers')->firstOrFail();
        $offer = $partner->offers->first();
        $partnerBooking = Booking::create([
            'code' => Booking::generateCode(),
            'type' => 'partner',
            'partner_id' => $partner->id,
            'partner_offer_id' => $offer->id,
            'nights' => $offer->isPerNight() ? 2 : null,
            'package_name' => $partner->name.' — '.$offer->tr('name', 'id'),
            'visit_date' => today()->addDays(3),
            'pax' => 2,
            'customer_name' => 'Pak Tamu Vila',
            'phone' => '081377778888',
            'group_type' => 'family',
            'price_per_pax' => $offer->price,
            'subtotal' => $offer->totalFor(2, 2),
            'addons_total' => 0,
            'total' => $offer->totalFor(2, 2),
            'commission_rate' => 10,
            'commission_amount' => (int) round($offer->totalFor(2, 2) * 0.1),
            'commission_status' => 'pending',
        ]);

        Lead::create([
            'name' => 'Pak Tamu Vila',
            'phone' => '081377778888',
            'source' => 'booking',
            'interest' => $partnerBooking->package_name,
            'partner_id' => $partner->id,
            'booking_id' => $partnerBooking->id,
            'estimated_value' => $partnerBooking->total,
        ]);

        WaClick::create(['context_type' => 'partner', 'context_id' => $partner->id, 'page' => '/jelajah']);
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
        $this->get(Reports::getUrl())->assertOk()->assertSee('Ekspor CSV (Excel)')->assertSee('Komisi mitra');
        $this->get(Monitoring::getUrl())->assertOk()->assertSee('Komisi per mitra');
        $this->get(LeadResource::getUrl('index', ['tab' => 'follow_up']))->assertOk();
        // Halaman detail & ubah booking mitra.
        $partnerBooking = Booking::query()->where('type', 'partner')->firstOrFail();
        $this->get(BookingResource::getUrl('view', ['record' => $partnerBooking]))->assertOk()->assertSee('Komisi mitra')->assertSee($partnerBooking->partner->name);
        $this->get(BookingResource::getUrl('edit', ['record' => $partnerBooking]))->assertOk()->assertSee('Status komisi');
    }

    public function test_relation_managers_render_for_every_owner(): void
    {
        $this->seedSampleData();
        $this->actingAsSuperAdmin();

        $owners = [
            [Package::query()->has('photos')->firstOrFail(), EditPackage::class],
            [Attraction::query()->has('photos')->firstOrFail(), EditAttraction::class],
            [Umkm::query()->has('photos')->firstOrFail(), EditUmkm::class],
            [Album::query()->has('photos')->firstOrFail(), EditAlbum::class],
        ];

        foreach ($owners as [$owner, $page]) {
            Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $owner, 'pageClass' => $page])
                ->assertOk()
                ->assertCanSeeTableRecords($owner->photos()->get())
                ->assertSee('Unggah banyak foto');
        }

        $partner = Partner::query()->has('bookings')->firstOrFail();
        Livewire::test(OffersRelationManager::class, ['ownerRecord' => $partner, 'pageClass' => EditPartner::class])
            ->assertOk()
            ->assertCanSeeTableRecords($partner->offers()->get());
        Livewire::test(PhotosRelationManager::class, ['ownerRecord' => $partner, 'pageClass' => EditPartner::class])
            ->assertOk();
        Livewire::test(BookingsRelationManager::class, ['ownerRecord' => $partner, 'pageClass' => EditPartner::class])
            ->assertOk()
            ->assertCanSeeTableRecords($partner->bookings()->get())
            ->assertSee('Belum ditagih');

        $umkm = Umkm::query()->has('products')->firstOrFail();
        Livewire::test(ProductsRelationManager::class, ['ownerRecord' => $umkm, 'pageClass' => EditUmkm::class])
            ->assertOk()
            ->assertCanSeeTableRecords($umkm->products()->get());
    }

    public function test_calendar_shows_bookings_and_blocked_dates(): void
    {
        $this->seedSampleData();
        $this->actingAsSuperAdmin();
        $booking = Booking::query()->firstOrFail();

        Livewire::test(BookingCalendar::class, ['month' => $booking->visit_date->format('Y-m')])
            ->assertSee($booking->code)
            ->assertSee('20 pax')
            ->call('nextMonth')
            ->assertSet('month', $booking->visit_date->copy()->addMonthNoOverflow()->format('Y-m'));

        $this->get(BookingCalendar::getUrl().'?bulan='.today()->addDays(7)->format('Y-m'))
            ->assertOk()
            ->assertSee('Acara desa');
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
