<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Attraction;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Package;
use App\Models\PartnerOffer;
use App\Models\PackageTier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Aturan booking: minimal peserta, tanggal ditutup, kuota harian,
 * batas waktu pemesanan, dan perhitungan estimasi harga.
 */
class BookingService
{
    public function leadDays(): int
    {
        return (int) setting('min_lead_days', 3);
    }

    public function maxAdvanceDays(): int
    {
        return (int) setting('max_advance_days', 365);
    }

    public function partnerLeadDays(): int
    {
        return (int) setting('partner_min_lead_days', 1);
    }

    public function earliestDate(bool $partner = false): Carbon
    {
        return today()->addDays($partner ? $this->partnerLeadDays() : $this->leadDays());
    }

    public function latestDate(): Carbon
    {
        return today()->addDays($this->maxAdvanceDays());
    }

    /**
     * Pesan kesalahan bila tanggal tidak bisa dipesan, atau null bila tersedia.
     */
    public function dateProblem(?Package $package, Carbon $date, int $pax, bool $partner = false): ?string
    {
        $date = $date->copy()->startOfDay();

        if ($date->lt($this->earliestDate($partner))) {
            return __('site.booking.errors.too_soon', ['days' => $partner ? $this->partnerLeadDays() : $this->leadDays()]);
        }

        if ($date->gt($this->latestDate())) {
            return __('site.booking.errors.too_far', ['days' => $this->maxAdvanceDays()]);
        }

        // Ketersediaan mitra (penginapan, tiket wisata) dikonfirmasi admin ke mitra lewat WhatsApp.
        if ($partner) {
            return null;
        }

        $blocked = BlockedDate::query()
            ->whereDate('date', $date)
            ->where(fn ($q) => $q->whereNull('package_id')->when($package, fn ($q) => $q->orWhere('package_id', $package->id)))
            ->first();

        if ($blocked) {
            return __('site.booking.errors.blocked', ['reason' => $blocked->reason ? ' ('.$blocked->reason.')' : '']);
        }

        if ($package?->daily_capacity) {
            $used = (int) Booking::query()->holdingCapacity()
                ->where('package_id', $package->id)
                ->whereDate('visit_date', $date)
                ->sum('pax');

            if ($used + $pax > $package->daily_capacity) {
                return __('site.booking.errors.full', ['left' => max(0, $package->daily_capacity - $used)]);
            }
        }

        $global = (int) setting('daily_capacity');
        if ($global > 0) {
            $used = (int) Booking::query()->holdingCapacity()->whereDate('visit_date', $date)->sum('pax');

            if ($used + $pax > $global) {
                return __('site.booking.errors.full', ['left' => max(0, $global - $used)]);
            }
        }

        return null;
    }

    /** Aktivitas yang bisa dipilih di paket custom. */
    public function customAttractions(): Collection
    {
        return Attraction::query()->active()->whereNotNull('custom_price')->get();
    }

    public function customBasePrice(): int
    {
        return (int) setting('custom_base_price', 0);
    }

    public function customMinPax(): int
    {
        return (int) setting('custom_min_pax', 1);
    }

    /**
     * Hitung estimasi harga. $data berisi type, package_tier_id / attraction_ids, pax, addons.
     *
     * @return array{type: string, package: ?Package, tier: ?PackageTier, package_name: string, min_pax: int,
     *     price_per_pax: int, subtotal: int, addons: array, addons_total: int, total: int, custom_items: ?array}
     */
    public function quote(array $data): array
    {
        $pax = max(0, (int) ($data['pax'] ?? 0));
        $type = in_array($data['type'] ?? null, ['custom', 'partner'], true) ? $data['type'] : 'package';
        $locale = app()->getLocale();

        $package = null;
        $tier = null;
        $offer = null;
        $nights = null;
        $customItems = null;

        if ($type === 'partner') {
            $offer = PartnerOffer::query()->with('partner')->where('is_active', true)->find($data['partner_offer_id'] ?? null);
            $nights = $offer?->isPerNight() ? max(1, (int) ($data['nights'] ?? 1)) : null;
            $pricePerPax = (int) ($offer?->price ?? 0);
            $minPax = (int) ($offer?->min_qty ?? 1);
            $packageName = $offer
                ? $offer->partner->name.' — '.$offer->tr('name', 'id').($nights ? ' ('.$nights.' malam)' : '')
                : '';
        } elseif ($type === 'custom') {
            $items = $this->customAttractions()->whereIn('id', (array) ($data['attraction_ids'] ?? []));
            $pricePerPax = $this->customBasePrice() + (int) $items->sum('custom_price');
            $minPax = $this->customMinPax();
            $customItems = $items->map(fn (Attraction $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'price' => $a->custom_price,
            ])->values()->all();
            $packageName = __('site.custom.package_name', [
                'items' => $items->map(fn ($a) => $a->tr('name', 'id'))->implode(', '),
            ], 'id');
        } else {
            $tier = PackageTier::query()->with('package')->find($data['package_tier_id'] ?? null);
            $package = $tier?->package;
            $pricePerPax = (int) ($tier?->price ?? 0);
            $minPax = (int) ($tier?->min_pax ?? 1);
            $packageName = $package
                ? $package->tr('name', 'id').(($package->tiers()->count() > 1) ? ' — '.$tier->tr('name', 'id') : '')
                : '';
        }

        $addons = Addon::query()->active()->whereIn('id', $type === 'partner' ? [] : (array) ($data['addons'] ?? []))->get()
            ->map(fn (Addon $addon) => [
                'id' => $addon->id,
                'name' => $addon->name,
                'unit' => $addon->unit,
                'price' => $addon->price,
                'subtotal' => $addon->totalFor($pax),
            ])->values()->all();

        $subtotal = $offer ? $offer->totalFor($pax, $nights) : $pricePerPax * $pax;
        $addonsTotal = (int) collect($addons)->sum('subtotal');
        $total = $subtotal + $addonsTotal;

        // Komisi hanya untuk penjualan mitra bisnis.
        $commissionRate = $offer ? (float) $offer->partner->commission_rate : null;
        $commissionAmount = $commissionRate ? (int) round($total * $commissionRate / 100) : 0;

        return [
            'type' => $type,
            'package' => $package,
            'tier' => $tier,
            'package_name' => $packageName,
            'min_pax' => $minPax,
            'price_per_pax' => $pricePerPax,
            'subtotal' => $subtotal,
            'addons' => $addons,
            'addons_total' => $addonsTotal,
            'total' => $total,
            'custom_items' => $customItems,
            'offer' => $offer,
            'partner' => $offer?->partner,
            'nights' => $nights,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'locale' => $locale,
        ];
    }

    public function create(array $data): Booking
    {
        $quote = $this->quote($data);

        $booking = Booking::create([
            'code' => Booking::generateCode(),
            'type' => $quote['type'],
            'package_id' => $quote['package']?->id,
            'package_tier_id' => $quote['tier']?->id,
            'partner_id' => $quote['partner']?->id,
            'partner_offer_id' => $quote['offer']?->id,
            'nights' => $quote['nights'],
            'package_name' => $quote['package_name'],
            'visit_date' => $data['visit_date'],
            'pax' => (int) $data['pax'],
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'organization' => $data['organization'] ?? null,
            'group_type' => $data['group_type'],
            'notes' => $data['notes'] ?? null,
            'locale' => app()->getLocale(),
            'price_per_pax' => $quote['price_per_pax'],
            'addons' => $quote['addons'],
            'custom_items' => $quote['custom_items'],
            'subtotal' => $quote['subtotal'],
            'addons_total' => $quote['addons_total'],
            'total' => $quote['total'],
            'commission_rate' => $quote['commission_rate'],
            'commission_amount' => $quote['commission_amount'],
            'commission_status' => $quote['commission_amount'] > 0 ? 'pending' : 'none',
            'status' => 'new',
        ]);

        Lead::create([
            'name' => $booking->customer_name,
            'phone' => $booking->phone,
            'email' => $booking->email,
            'source' => 'booking',
            'status' => 'new',
            'interest' => $booking->package_name,
            'package_id' => $booking->package_id,
            'partner_id' => $booking->partner_id,
            'booking_id' => $booking->id,
            'estimated_value' => $booking->total,
            'locale' => $booking->locale,
        ]);

        return $booking;
    }
}
