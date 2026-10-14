<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Reports;
use App\Filament\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use Database\Seeders\SettingSeeder;
use Database\Seeders\TourismSeeder;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

class BookingAdminTest extends AdminTestCase
{
    protected function makeBooking(array $attributes = []): Booking
    {
        return Booking::create([
            'code' => Booking::generateCode(),
            'package_name' => 'Paket Edukasi Budidaya Kelinci — Hemat',
            'visit_date' => today()->addDays(10),
            'pax' => 20,
            'customer_name' => 'Pak Andi',
            'phone' => '0812-3456-7890',
            'organization' => 'SMP Tamansari',
            'group_type' => 'school',
            'price_per_pax' => 65000,
            'addons' => [['id' => 9, 'name' => ['id' => 'Oleh-oleh', 'en' => 'Souvenir'], 'unit' => 'per_pax', 'price' => 10000, 'subtotal' => 200000]],
            'subtotal' => 1300000,
            'addons_total' => 200000,
            'total' => 1500000,
            ...$attributes,
        ]);
    }

    public function test_confirm_action_sets_status_and_confirmed_at(): void
    {
        $this->seed(SettingSeeder::class);
        $this->actingAsSuperAdmin();
        $booking = $this->makeBooking();

        Livewire::test(ListBookings::class)
            ->assertCanSeeTableRecords([$booking])
            ->callAction(TestAction::make('confirm')->table($booking));

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertNotNull($booking->confirmed_at);
    }

    public function test_cancel_and_complete_actions(): void
    {
        $this->actingAsSuperAdmin();
        $cancel = $this->makeBooking();
        $complete = $this->makeBooking(['status' => 'confirmed', 'confirmed_at' => now()]);

        Livewire::test(ListBookings::class)
            ->callAction(TestAction::make('cancel')->table($cancel))
            ->callAction(TestAction::make('complete')->table($complete));

        $this->assertSame('cancelled', $cancel->refresh()->status);
        $this->assertSame('completed', $complete->refresh()->status);
    }

    public function test_whatsapp_action_links_to_customer_number(): void
    {
        $this->seed(SettingSeeder::class);
        $this->actingAsSuperAdmin();
        $booking = $this->makeBooking()->refresh();

        Livewire::test(ListBookings::class)
            ->assertActionHasUrl(TestAction::make('whatsapp')->table($booking), wa_link($booking->phone, $booking->adminWhatsappMessage()));

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', wa_link($booking->phone, $booking->adminWhatsappMessage()));
    }

    public function test_admin_can_create_manual_booking_with_computed_totals(): void
    {
        $this->seed([SettingSeeder::class, TourismSeeder::class]);
        $this->actingAsSuperAdmin();

        $package = Package::query()->whereHas('tiers', null, '>', 1)->with('tiers')->firstOrFail();
        $tier = $package->tiers->first();
        $perGroup = Addon::query()->where('unit', 'per_group')->first();
        $perPax = Addon::query()->where('unit', 'per_pax')->first();
        $addonIds = array_values(array_filter([$perGroup?->id, $perPax?->id]));

        Livewire::test(CreateBooking::class)
            ->fillForm([
                'package_id' => $package->id,
                'package_tier_id' => $tier->id,
                'visit_date' => today()->addDays(14)->format('Y-m-d'),
                'pax' => 25,
                'status' => 'new',
                'customer_name' => 'Bu Rina',
                'phone' => '081311112222',
                'group_type' => 'school',
                'locale' => 'id',
                'addon_ids' => $addonIds,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $booking = Booking::query()->where('customer_name', 'Bu Rina')->firstOrFail();
        $addonsTotal = ($perGroup?->price ?? 0) + (($perPax?->price ?? 0) * 25);

        $this->assertMatchesRegularExpression('/^SKJ-\d{6}-[A-Z0-9]{4}$/', $booking->code);
        $this->assertSame($package->id, $booking->package_id);
        $this->assertSame($tier->price, $booking->price_per_pax);
        $this->assertSame($tier->price * 25, $booking->subtotal);
        $this->assertSame($addonsTotal, $booking->addons_total);
        $this->assertSame($tier->price * 25 + $addonsTotal, $booking->total);
        $this->assertStringContainsString($package->tr('name', 'id'), $booking->package_name);
        $this->assertCount(count($addonIds), $booking->addons);
    }

    public function test_editing_pax_recalculates_totals(): void
    {
        $this->actingAsSuperAdmin();
        $booking = $this->makeBooking();

        Livewire::test(EditBooking::class, ['record' => $booking->getRouteKey()])
            ->fillForm(['pax' => 30, 'admin_notes' => 'Sudah transfer DP'])
            ->call('save')
            ->assertHasNoFormErrors();

        $booking->refresh();
        $this->assertSame(30, $booking->pax);
        $this->assertSame(65000 * 30, $booking->subtotal);
        $this->assertSame(10000 * 30, $booking->addons_total);
        $this->assertSame(65000 * 30 + 10000 * 30, $booking->total);
        $this->assertSame('Sudah transfer DP', $booking->admin_notes);
    }

    public function test_reports_csv_export_downloads(): void
    {
        $this->actingAsSuperAdmin();
        $this->makeBooking(['visit_date' => now()->startOfYear()->addDays(40), 'status' => 'confirmed', 'customer_name' => 'Pak Ekspor']);
        $this->makeBooking(['visit_date' => now()->startOfYear()->addDays(41), 'status' => 'cancelled']);

        $test = Livewire::test(Reports::class, ['year' => now()->year])
            ->callAction('export')
            ->assertFileDownloaded('booking-sukajadi-'.now()->year.'.csv');

        $content = base64_decode(data_get($test->effects, 'download.content'));
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('Kode;"Tanggal kunjungan";Paket', $content);
        $this->assertStringContainsString('Pak Ekspor', $content);
        $this->assertSame(3, substr_count(trim($content), "\n") + 1);
    }

    public function test_reports_summary_excludes_cancelled(): void
    {
        $this->actingAsSuperAdmin();
        $this->makeBooking(['visit_date' => now()->startOfYear()->addDays(3), 'status' => 'completed', 'pax' => 20, 'total' => 1500000]);
        $this->makeBooking(['visit_date' => now()->startOfYear()->addDays(4), 'status' => 'cancelled', 'pax' => 50, 'total' => 9000000]);

        $this->get(Reports::getUrl().'?tahun='.now()->year)
            ->assertOk()
            ->assertSee('Rp1.500.000')
            ->assertDontSee('Rp9.000.000');
    }
}
