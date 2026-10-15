<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Monitoring;
use App\Filament\Pages\Settings;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\RelationManagers\OffersRelationManager;
use App\Filament\Widgets\BookingStats;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Partner;
use App\Models\PartnerOffer;
use App\Models\Setting;
use App\Models\WaClick;
use Database\Seeders\SettingSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class PartnerLeadMonitoringTest extends AdminTestCase
{
    protected function makePartner(array $attributes = []): Partner
    {
        return Partner::create([
            'slug' => 'vila-uji',
            'slug_en' => 'test-villa',
            'name' => 'Vila Uji',
            'category' => 'penginapan',
            'commission_rate' => 10,
            'agreement_status' => 'active',
            ...$attributes,
        ]);
    }

    protected function makePartnerBooking(Partner $partner, array $attributes = []): Booking
    {
        $offer = $partner->offers()->first() ?? $partner->offers()->create([
            'name' => ['id' => 'Vila 2 kamar', 'en' => '2-bedroom villa'],
            'price' => 750000,
            'unit' => 'per_night',
        ]);

        return Booking::create([
            'code' => Booking::generateCode(),
            'type' => 'partner',
            'partner_id' => $partner->id,
            'partner_offer_id' => $offer->id,
            'nights' => 2,
            'package_name' => $partner->name.' — Vila 2 kamar (2 malam)',
            'visit_date' => today()->addDays(5),
            'pax' => 1,
            'customer_name' => 'Bu Tamu',
            'phone' => '081299990000',
            'group_type' => 'family',
            'price_per_pax' => 750000,
            'subtotal' => 1500000,
            'addons_total' => 0,
            'total' => 1500000,
            'commission_rate' => 10,
            'commission_amount' => 150000,
            'commission_status' => 'pending',
            ...$attributes,
        ]);
    }

    public function test_create_partner_with_offer_persists_bilingual_json(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(CreatePartner::class)
            ->fillForm([
                'name' => 'Curug Nangka',
                'category' => 'wisata',
                'commission_rate' => 12.5,
                'summary' => ['id' => 'Air terjun di kaki Gunung Salak', 'en' => 'Waterfall at the foot of Mount Salak'],
                'opening_hours' => ['id' => 'Setiap hari', 'en' => 'Daily'],
                'highlights' => [
                    ['id' => 'Tiga air terjun', 'en' => 'Three waterfalls'],
                    ['id' => 'Area berkemah', 'en' => null],
                ],
                'contact_name' => 'Pak Ujang',
                'contact_phone' => '081200001111',
                'agreement_status' => 'active',
                'internal_notes' => 'Komisi disepakati lisan.',
            ])
            ->assertSchemaStateSet(['slug' => 'curug-nangka'])
            ->call('create')
            ->assertHasNoFormErrors();

        $partner = Partner::query()->where('slug', 'curug-nangka')->firstOrFail();
        $this->assertSame('curug-nangka', $partner->slug_en);
        $this->assertSame('Waterfall at the foot of Mount Salak', $partner->summary['en']);
        $this->assertSame(12.5, $partner->commission_rate);
        $this->assertSame([['id' => 'Tiga air terjun', 'en' => 'Three waterfalls'], ['id' => 'Area berkemah', 'en' => null]], $partner->highlights);
        $this->assertSame('active', $partner->agreement_status);

        Livewire::test(OffersRelationManager::class, ['ownerRecord' => $partner, 'pageClass' => EditPartner::class])
            ->callAction(TestAction::make('create')->table(), [
                'name' => ['id' => 'Tiket masuk', 'en' => 'Entrance ticket'],
                'description' => ['id' => 'Termasuk parkir', 'en' => 'Parking included'],
                'price' => 25000,
                'unit' => 'per_ticket',
                'min_qty' => 1,
                'is_dummy_price' => true,
                'is_active' => true,
            ])
            ->assertHasNoFormErrors()
            ->assertSee('Harga sementara');

        $offer = PartnerOffer::query()->where('partner_id', $partner->id)->firstOrFail();
        $this->assertSame(['id' => 'Tiket masuk', 'en' => 'Entrance ticket'], $offer->name);
        $this->assertSame('Parking included', $offer->tr('description', 'en'));
        $this->assertSame(25000, $offer->price);
        $this->assertTrue($offer->is_dummy_price);
    }

    public function test_partner_table_shows_offer_count_and_commission_total(): void
    {
        $this->actingAsSuperAdmin();
        $partner = $this->makePartner();
        $this->makePartnerBooking($partner);
        $this->makePartnerBooking($partner, ['status' => 'cancelled', 'commission_amount' => 999000, 'commission_status' => 'none']);

        Livewire::test(ListPartners::class)
            ->assertCanSeeTableRecords([$partner])
            ->assertTableColumnStateSet('offers_count', 1, $partner)
            ->assertTableColumnStateSet('bookings_count', 1, $partner)
            ->assertTableColumnStateSet('commission_total', 150000, $partner)
            ->assertSee('Rp150.000');
    }

    public function test_commission_actions_on_bookings(): void
    {
        $this->actingAsSuperAdmin();
        $partner = $this->makePartner();
        $one = $this->makePartnerBooking($partner);
        $two = $this->makePartnerBooking($partner);
        $three = $this->makePartnerBooking($partner);

        Livewire::test(ListBookings::class)
            ->filterTable('type', 'partner')
            ->assertCanSeeTableRecords([$one, $two, $three])
            ->callAction(TestAction::make('commissionInvoiced')->table($one))
            ->assertActionHidden(TestAction::make('commissionInvoiced')->table($one))
            ->selectTableRecords([$two, $three])
            ->callAction(TestAction::make('commissionPaidBulk')->table()->bulk());

        $this->assertSame('invoiced', $one->refresh()->commission_status);
        $this->assertSame('paid', $two->refresh()->commission_status);
        $this->assertSame('paid', $three->refresh()->commission_status);

        Livewire::test(ListBookings::class)
            ->filterTable('commission_status', ['invoiced'])
            ->assertCanSeeTableRecords([$one])
            ->assertCanNotSeeTableRecords([$two, $three]);
    }

    public function test_editing_partner_booking_updates_commission_status_and_recalculates(): void
    {
        $this->actingAsSuperAdmin();
        $booking = $this->makePartnerBooking($this->makePartner());

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->assertSchemaStateSet(['commission_status' => 'pending', 'nights' => 2])
            ->fillForm(['nights' => 3, 'commission_status' => 'invoiced'])
            ->call('save')
            ->assertHasNoFormErrors();

        $booking->refresh();
        $this->assertSame(3, $booking->nights);
        $this->assertSame(2250000, $booking->total);
        $this->assertSame(225000, $booking->commission_amount);
        $this->assertSame('invoiced', $booking->commission_status);
        $this->assertStringEndsWith('(3 malam)', $booking->package_name);
    }

    public function test_lead_status_and_claim_actions(): void
    {
        $this->seed(SettingSeeder::class);
        $user = $this->actingAsSuperAdmin();
        $lead = Lead::create(['name' => 'Bu Wati', 'phone' => '0812-1111-2222', 'source' => 'contact', 'follow_up_at' => today()->subDay()]);
        $won = Lead::create(['name' => 'Pak Dedi', 'source' => 'manual', 'status' => 'won']);

        Livewire::test(ListLeads::class)
            ->assertCanSeeTableRecords([$lead, $won])
            ->assertActionHasUrl(TestAction::make('whatsapp')->table($lead), wa_link($lead->phone, LeadResource::whatsappMessage($lead)))
            ->callAction(TestAction::make('status_contacted')->table($lead))
            ->set('activeTab', 'follow_up')
            ->assertCanSeeTableRecords([$lead])
            ->assertCanNotSeeTableRecords([$won]);

        $lead->refresh();
        $this->assertSame('contacted', $lead->status);
        $this->assertSame($user->id, $lead->assigned_to);
        $this->assertStringStartsWith('https://wa.me/6281211112222?text=Halo%20Bu%20Wati', wa_link($lead->phone, LeadResource::whatsappMessage($lead)));

        $other = $this->admin();
        $lead->update(['assigned_to' => $other->id]);

        Livewire::test(ListLeads::class)
            ->callAction(TestAction::make('claim')->table($lead))
            ->callAction(TestAction::make('status_won')->table($lead));

        $lead->refresh();
        $this->assertSame($user->id, $lead->assigned_to);
        $this->assertSame('won', $lead->status);
        $this->assertNull($lead->follow_up_at);
        $this->assertNull(LeadResource::getNavigationBadge());
    }

    public function test_lead_edit_form_saves_and_links_booking(): void
    {
        $this->actingAsSuperAdmin();
        $partner = $this->makePartner();
        $booking = $this->makePartnerBooking($partner);
        $lead = Lead::create(['name' => 'Bu Tamu', 'source' => 'booking', 'booking_id' => $booking->id, 'partner_id' => $partner->id]);
        $assignee = $this->admin();

        Livewire::test(EditLead::class, ['record' => $lead->getRouteKey()])
            ->assertSee($booking->code)
            ->fillForm([
                'status' => 'negotiating',
                'assigned_to' => $assignee->id,
                'follow_up_at' => today()->addDays(2)->format('Y-m-d'),
                'estimated_value' => 1500000,
                'notes' => 'Minta diskon rombongan.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $lead->refresh();
        $this->assertSame('negotiating', $lead->status);
        $this->assertSame($assignee->id, $lead->assigned_to);
        $this->assertSame(1500000, $lead->estimated_value);

        // Booking dikonfirmasi → prospek otomatis berhasil.
        $booking->update(['status' => 'confirmed']);
        $this->assertSame('won', $lead->refresh()->status);
    }

    public function test_monitoring_page_renders_stats_and_exports_commission_csv(): void
    {
        $this->seed();
        $this->actingAsSuperAdmin();

        $partner = $this->makePartner(['name' => 'Highland Uji', 'slug' => 'highland-uji', 'slug_en' => 'highland-test']);
        $this->makePartnerBooking($partner, ['status' => 'confirmed', 'commission_status' => 'invoiced']);
        $this->makePartnerBooking($partner);
        $this->makePartnerBooking($partner, ['status' => 'cancelled', 'commission_amount' => 777000, 'commission_status' => 'none']);
        $offer = $partner->offers()->first();
        $package = Package::query()->firstOrFail();

        Lead::create(['name' => 'A', 'source' => 'booking', 'status' => 'won']);
        Lead::create(['name' => 'B', 'source' => 'contact', 'status' => 'new']);
        Lead::create(['name' => 'C', 'source' => 'whatsapp', 'status' => 'lost']);
        Lead::create(['name' => 'D', 'source' => 'manual', 'status' => 'contacted']);

        WaClick::create(['context_type' => 'package', 'context_id' => $package->id]);
        WaClick::create(['context_type' => 'package', 'context_id' => $package->id]);
        WaClick::create(['context_type' => 'offer', 'context_id' => $offer->id]);
        WaClick::create(['context_type' => 'general']);
        WaClick::create(['context_type' => 'partner', 'context_id' => 99999]);
        // Di luar periode 30 hari.
        WaClick::create(['context_type' => 'package', 'context_id' => $package->id, 'created_at' => now()->subDays(60)]);

        $this->get(Monitoring::getUrl())
            ->assertOk()
            ->assertSee('Corong prospek')
            ->assertSee('25,0%')
            ->assertSee($package->tr('name', 'id'))
            ->assertSee('Vila 2 kamar — Highland Uji')
            ->assertSee('Tombol WhatsApp umum')
            ->assertSee('sudah dihapus')
            ->assertSee('Highland Uji')
            ->assertSee('Rp300.000')
            ->assertDontSee('Rp777.000');

        $page = Livewire::test(Monitoring::class);
        $rows = $page->instance()->commissionPerPartner();
        $row = $rows->firstWhere('name', 'Highland Uji');
        $this->assertSame(2, $row['bookings']);
        $this->assertSame(3000000, $row['gross']);
        $this->assertSame(300000, $row['commission']);
        $this->assertSame(150000, $row['pending']);
        $this->assertSame(150000, $row['invoiced']);
        $this->assertSame(0, $row['paid']);

        $test = $page->set('period', 'year')
            ->callAction('exportCommission')
            ->assertFileDownloaded('komisi-mitra-'.now()->startOfYear()->format('Ymd').'-'.today()->format('Ymd').'.csv');

        $content = base64_decode(data_get($test->effects, 'download.content'));
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Mitra;"Komisi (%)";Booking', $content);
        $this->assertStringContainsString('"Highland Uji";10;2;3000000;300000;150000;150000;0', $content);
    }

    public function test_dashboard_stats_show_lead_wa_and_commission(): void
    {
        $this->actingAsSuperAdmin();
        $this->makePartnerBooking($this->makePartner());
        Lead::create(['name' => 'A', 'source' => 'booking', 'status' => 'new']);
        WaClick::create(['context_type' => 'general']);

        Livewire::test(BookingStats::class)
            ->assertSee('Prospek baru')
            ->assertSee('Konversi 30 hari')
            ->assertSee('Klik WhatsApp 7 hari')
            ->assertSee('Komisi belum ditagih')
            ->assertSee('Rp150.000');
    }

    public function test_settings_saves_home_fields_and_hero_slides(): void
    {
        Storage::fake('uploads');
        $this->seed(SettingSeeder::class);
        $this->actingAsSuperAdmin();

        Livewire::test(Settings::class)
            ->assertSchemaStateSet(['story_title.id' => 'Cerita dari Sukajadi', 'partner_min_lead_days' => 1])
            ->fillForm([
                'story_title' => ['id' => 'Kisah Sukajadi', 'en' => 'The Sukajadi story'],
                'managed_by' => ['id' => 'Dikelola penggiat desa', 'en' => 'Run by the village team'],
                'partner_min_lead_days' => 2,
                'hero_slides' => [
                    UploadedFile::fake()->image('slide-1.jpg', 1200, 800),
                    UploadedFile::fake()->image('slide-2.jpg', 1200, 800),
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['id' => 'Kisah Sukajadi', 'en' => 'The Sukajadi story'], Setting::get('story_title'));
        $this->assertSame('Dikelola penggiat desa', Setting::get('managed_by')['id']);
        $this->assertSame(2, Setting::get('partner_min_lead_days'));
        $this->assertStringContainsString('<p>', Setting::get('story_body')['id']);

        $slides = Setting::get('hero_slides');
        $this->assertIsArray($slides);
        $this->assertCount(2, $slides);
        $this->assertTrue(array_is_list($slides));
        foreach ($slides as $path) {
            $this->assertStringStartsWith('settings/', $path);
            Storage::disk('uploads')->assertExists($path);
        }

        // Menghapus satu foto dari slideshow ikut menghapus filenya.
        $removed = $slides[0];
        Livewire::test(Settings::class)
            ->fillForm(['hero_slides' => [$slides[1]]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([$slides[1]], Setting::get('hero_slides'));
        Storage::disk('uploads')->assertMissing($removed);
    }
}
