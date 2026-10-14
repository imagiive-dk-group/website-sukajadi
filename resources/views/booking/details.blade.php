<dl class="divide-y divide-leaf-900/5 text-sm">
    <div class="flex justify-between gap-4 py-2.5"><dt class="text-leaf-950/60">{{ __('site.booking.package') }}</dt><dd class="text-right font-semibold">{{ $booking->package_name }}</dd></div>
    <div class="flex justify-between gap-4 py-2.5"><dt class="text-leaf-950/60">{{ __('site.booking.date') }}</dt><dd class="text-right font-semibold">{{ $booking->visit_date->locale(app()->getLocale())->translatedFormat('l, j F Y') }}</dd></div>
    <div class="flex justify-between gap-4 py-2.5"><dt class="text-leaf-950/60">{{ __('site.booking.pax') }}</dt><dd class="font-semibold">{{ $booking->pax }}</dd></div>
    <div class="flex justify-between gap-4 py-2.5"><dt class="text-leaf-950/60">{{ __('site.booking.name') }}</dt><dd class="text-right font-semibold">{{ $booking->customer_name }}@if($booking->organization) · {{ $booking->organization }}@endif</dd></div>
    <div class="flex justify-between gap-4 py-2.5"><dt class="text-leaf-950/60">{{ __('site.booking.subtotal') }} ({{ rupiah($booking->price_per_pax) }} × {{ $booking->pax }})</dt><dd class="font-semibold">{{ rupiah($booking->subtotal) }}</dd></div>
    @foreach($booking->addons ?? [] as $addon)
        <div class="flex justify-between gap-4 py-2.5"><dt class="text-leaf-950/60">+ {{ localized($addon['name']) }}</dt><dd class="font-semibold">{{ rupiah($addon['subtotal']) }}</dd></div>
    @endforeach
    <div class="flex justify-between gap-4 py-3"><dt class="font-semibold">{{ __('site.booking.total') }}</dt><dd class="font-display text-xl font-extrabold text-leaf-700">{{ rupiah($booking->total) }}</dd></div>
</dl>
