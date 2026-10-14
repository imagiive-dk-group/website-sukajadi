<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Attraction;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Package;
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

    public function earliestDate(): Carbon
    {
        return today()->addDays($this->leadDays());
    }

    public function latestDate(): Carbon
    {
        return today()->addDays($this->maxAdvanceDays());
    }

    /**
     * Pesan kesalahan bila tanggal tidak bisa dipesan, atau null bila tersedia.
     */
    public function dateProblem(?Package $package, Carbon $date, int $pax): ?string
    {
        $date = $date->copy()->startOfDay();

        if ($date->lt($this->earliestDate())) {
            return __('site.booking.errors.too_soon', ['days' => $this->leadDays()]);
        }

        if ($date->gt($this->latestDate())) {
            return __('site.booking.errors.too_far', ['days' => $this->maxAdvanceDays()]);
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
        $type = ($data['type'] ?? 'package') === 'custom' ? 'custom' : 'package';
        $locale = app()->getLocale();

        $package = null;
        $tier = null;
        $customItems = null;

        if ($type === 'custom') {
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

        $addons = Addon::query()->active()->whereIn('id', (array) ($data['addons'] ?? []))->get()
            ->map(fn (Addon $addon) => [
                'id' => $addon->id,
                'name' => $addon->name,
                'unit' => $addon->unit,
                'price' => $addon->price,
                'subtotal' => $addon->totalFor($pax),
            ])->values()->all();

        $subtotal = $pricePerPax * $pax;
        $addonsTotal = (int) collect($addons)->sum('subtotal');

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
            'total' => $subtotal + $addonsTotal,
            'custom_items' => $customItems,
            'locale' => $locale,
        ];
    }

    public function create(array $data): Booking
    {
        $quote = $this->quote($data);

        return Booking::create([
            'code' => Booking::generateCode(),
            'type' => $quote['type'],
            'package_id' => $quote['package']?->id,
            'package_tier_id' => $quote['tier']?->id,
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
            'status' => 'new',
        ]);
    }
}
