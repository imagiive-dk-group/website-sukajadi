<?php

namespace Tests\Feature\Site;

use App\Mail\BookingReceived;
use App\Models\Attraction;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageTier;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
    }

    protected function premiumTier(): PackageTier
    {
        return Package::where('slug', 'budidaya-kelinci')->first()->tiers()->where('price', 125000)->first();
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'type' => 'package',
            'package_tier_id' => $this->premiumTier()->id,
            'visit_date' => today()->addDays(10)->toDateString(),
            'pax' => 40,
            'customer_name' => 'Bu Rina',
            'phone' => '0812-3456-7890',
            'email' => 'rina@example.com',
            'organization' => 'SDN Contoh',
            'group_type' => 'school',
            'agree' => '1',
        ], $overrides);
    }

    public function test_booking_form_prefills_package(): void
    {
        $tier = $this->premiumTier();

        $this->get(route('id.booking.create', ['paket' => 'budidaya-kelinci', 'tier' => $tier->id]))
            ->assertOk()
            ->assertSee('package_tier_id\\u0022:'.$tier->id, false);
    }

    public function test_package_booking_is_created_with_estimate_and_notifications(): void
    {
        $response = $this->post(route('id.booking.store'), $this->payload(['addons' => [1]]));

        $booking = Booking::first();
        $response->assertRedirect();
        $this->assertStringContainsString('/booking/terima-kasih/'.$booking->code, $response->headers->get('Location'));

        $this->assertMatchesRegularExpression('/^SKJ-\d{6}-[A-Z2-9]{4}$/', $booking->code);
        $this->assertSame('new', $booking->status);
        $this->assertSame('Paket Edukasi Budidaya Kelinci — Premium', $booking->package_name);
        $this->assertSame(125000 * 40, $booking->subtotal);
        $this->assertSame(500000, $booking->addons_total);
        $this->assertSame(125000 * 40 + 500000, $booking->total);

        Mail::assertSent(BookingReceived::class, fn ($mail) => $mail->forAdmin && $mail->hasTo('admin@desawisatasukajadi.com'));
        Mail::assertSent(BookingReceived::class, fn ($mail) => ! $mail->forAdmin && $mail->hasTo('rina@example.com'));

        $this->followRedirects($response)->assertOk()->assertSee($booking->code)->assertSee('wa.me/6285775194575', false);
    }

    public function test_thank_you_page_requires_signature(): void
    {
        $this->post(route('id.booking.store'), $this->payload());
        $booking = Booking::first();

        $this->get('/booking/terima-kasih/'.$booking->code)->assertForbidden();
    }

    public function test_minimum_pax_is_enforced(): void
    {
        $this->post(route('id.booking.store'), $this->payload(['pax' => 10]))
            ->assertSessionHasErrors(['pax' => 'Minimal peserta untuk paket ini 15 pax.']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_lead_days_are_enforced(): void
    {
        $this->post(route('id.booking.store'), $this->payload(['visit_date' => today()->addDay()->toDateString()]))
            ->assertSessionHasErrors('visit_date');
    }

    public function test_blocked_dates_are_rejected(): void
    {
        $date = today()->addDays(12);
        BlockedDate::create(['date' => $date, 'reason' => 'Acara desa']);

        $this->post(route('id.booking.store'), $this->payload(['visit_date' => $date->toDateString()]))
            ->assertSessionHasErrors('visit_date');

        $this->getJson(route('booking.availability', ['date' => $date->toDateString(), 'pax' => 20, 'locale' => 'en']))
            ->assertOk()->assertJson(['ok' => false])->assertJsonFragment(['message' => 'Sorry, this date is not available (Acara desa). Please choose another date.']);
    }

    public function test_daily_capacity_is_enforced_when_set(): void
    {
        Setting::put('daily_capacity', 60);
        $date = today()->addDays(15)->toDateString();

        $this->post(route('id.booking.store'), $this->payload(['visit_date' => $date, 'pax' => 40]))->assertSessionHasNoErrors();
        $this->post(route('id.booking.store'), $this->payload(['visit_date' => $date, 'pax' => 30]))->assertSessionHasErrors('visit_date');

        // Booking yang dibatalkan tidak memakai kuota.
        Booking::query()->update(['status' => 'cancelled']);
        $this->post(route('id.booking.store'), $this->payload(['visit_date' => $date, 'pax' => 30]))->assertSessionHasNoErrors();
    }

    public function test_package_capacity_is_enforced(): void
    {
        $this->premiumTier()->package->update(['daily_capacity' => 50]);
        $date = today()->addDays(20)->toDateString();

        $this->post(route('id.booking.store'), $this->payload(['visit_date' => $date, 'pax' => 40]))->assertSessionHasNoErrors();
        $this->post(route('id.booking.store'), $this->payload(['visit_date' => $date, 'pax' => 15]))->assertSessionHasErrors('visit_date');
    }

    public function test_custom_package_booking_is_priced_from_selected_activities(): void
    {
        $items = Attraction::whereNotNull('custom_price')->take(2)->get();

        $this->post(route('en.booking.store'), $this->payload([
            'type' => 'custom',
            'package_tier_id' => null,
            'attraction_ids' => $items->pluck('id')->all(),
            'pax' => 20,
        ]))->assertSessionHasNoErrors();

        $booking = Booking::first();
        $perPax = 35000 + $items->sum('custom_price');
        $this->assertSame('custom', $booking->type);
        $this->assertSame('en', $booking->locale);
        $this->assertSame($perPax, $booking->price_per_pax);
        $this->assertSame($perPax * 20, $booking->total);
        $this->assertStringStartsWith('Paket Custom:', $booking->package_name);
        $this->assertCount(2, $booking->custom_items);
    }

    public function test_check_booking_requires_matching_phone(): void
    {
        $this->post(route('id.booking.store'), $this->payload());
        $booking = Booking::first();

        $this->get(route('id.booking.check', ['code' => strtolower($booking->code), 'phone' => '+62 812 3456 7890']))
            ->assertOk()->assertSee($booking->package_name)->assertSee('Baru');

        $this->get(route('id.booking.check', ['code' => $booking->code, 'phone' => '0899999999']))
            ->assertOk()->assertSee('Booking tidak ditemukan')->assertDontSee($booking->package_name);
    }

    public function test_whatsapp_messages_follow_booking_language(): void
    {
        $this->post(route('en.booking.store'), $this->payload());
        $booking = Booking::first();

        $this->assertStringContainsString('I would like to confirm my booking', $booking->customerWhatsappMessage());
        $this->assertStringContainsString($booking->code, $booking->adminWhatsappMessage());
    }
}
