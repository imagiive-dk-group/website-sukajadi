<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingReceived;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Partner;
use App\Services\BookingService;
use App\Support\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class BookingController extends Controller
{
    public function create(Request $request, BookingService $service)
    {
        $packages = Package::query()->active()->with('tiers')->get();
        $attractions = $service->customAttractions();
        $partners = Partner::query()->active()->with(['offers' => fn ($q) => $q->where('is_active', true)])->get()
            ->filter(fn (Partner $p) => $p->offers->isNotEmpty())->values();

        // Prefill dari halaman paket (?paket=slug&tier=id) atau paket custom (?custom=1&items[]=..).
        $preset = [
            'type' => $request->boolean('custom') ? 'custom' : ($request->filled('mitra') ? 'partner' : 'package'),
            'package_id' => null,
            'partner_id' => null,
            'partner_offer_id' => $request->integer('offer') ?: null,
            'nights' => $request->integer('nights') ?: null,
            'package_tier_id' => $request->integer('tier') ?: null,
            'attraction_ids' => array_values(array_map('intval', (array) $request->query('items', []))),
            'pax' => $request->integer('pax') ?: null,
        ];

        if ($slug = $request->query('mitra')) {
            $partner = $partners->first(fn (Partner $p) => $p->slug === $slug || $p->slug_en === $slug);
            if ($partner) {
                $preset['partner_id'] = $partner->id;
                if (! $partner->offers->contains('id', $preset['partner_offer_id'])) {
                    $preset['partner_offer_id'] = $partner->offers->first()?->id;
                }
            }
        } elseif ($slug = $request->query('paket')) {
            $package = $packages->first(fn (Package $p) => $p->slug === $slug || $p->slug_en === $slug);
            if ($package) {
                $preset['package_id'] = $package->id;
                if (! $package->tiers->contains('id', $preset['package_tier_id'])) {
                    $preset['package_tier_id'] = $package->tiers->first()?->id;
                }
            }
        } elseif ($preset['package_tier_id']) {
            $preset['package_id'] = $packages->first(fn ($p) => $p->tiers->contains('id', $preset['package_tier_id']))?->id;
        }

        return view('booking.create', [
            'packages' => $packages,
            'attractions' => $attractions,
            'partners' => $partners,
            'partnerEarliest' => $service->earliestDate(true),
            'addons' => Addon::query()->active()->get(),
            'preset' => $preset,
            'earliest' => $service->earliestDate(),
            'latest' => $service->latestDate(),
            'leadDays' => $service->leadDays(),
            'customBase' => $service->customBasePrice(),
            'customMinPax' => $service->customMinPax(),
        ]);
    }

    public function store(StoreBookingRequest $request, BookingService $service)
    {
        $booking = $service->create($request->validated());

        Notifier::admins(new BookingReceived($booking, forAdmin: true));
        Notifier::to($booking->email, new BookingReceived($booking));

        return redirect()->to(URL::signedRoute(app()->getLocale().'.booking.done', ['booking' => $booking->code]));
    }

    public function done(Booking $booking)
    {
        return view('booking.done', ['booking' => $booking]);
    }

    public function check(Request $request)
    {
        $booking = null;
        $searched = $request->filled(['code', 'phone']);

        if ($searched) {
            $code = strtoupper(trim((string) $request->query('code')));
            $phone = wa_number((string) $request->query('phone'));

            $booking = Booking::query()->where('code', $code)->first();
            if ($booking && wa_number($booking->phone) !== $phone) {
                $booking = null;
            }
        }

        return view('booking.check', compact('booking', 'searched'));
    }

    /** Dipakai formulir booking untuk mengecek tanggal secara langsung. */
    public function availability(Request $request, BookingService $service)
    {
        $locale = in_array($request->query('locale'), ['id', 'en'], true) ? $request->query('locale') : 'id';
        app()->setLocale($locale);

        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'pax' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'package_id' => ['nullable', 'integer'],
        ]);

        $package = $request->filled('package_id') ? Package::query()->find($request->integer('package_id')) : null;
        $problem = $service->dateProblem($package, Carbon::parse($request->query('date')), $request->integer('pax'), $request->boolean('partner'));

        return response()->json([
            'ok' => $problem === null,
            'message' => $problem ?? __('site.booking.available'),
        ]);
    }
}
