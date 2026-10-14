<x-mail::message>
# {{ $booking->code }}

{{ $forAdmin ? __('site.mail.admin_intro') : __('site.mail.customer_intro') }}

<x-mail::table>
| | |
|:--|:--|
| {{ __('site.booking.package') }} | {{ $booking->package_name }} |
| {{ __('site.booking.date') }} | {{ $booking->visit_date->locale(app()->getLocale())->translatedFormat('l, j F Y') }} |
| {{ __('site.booking.pax') }} | {{ $booking->pax }} |
| {{ __('site.booking.name') }} | {{ $booking->customer_name }} |
| {{ __('site.booking.phone') }} | {{ $booking->phone }} |
@if($booking->organization)
| {{ __('site.booking.organization') }} | {{ $booking->organization }} |
@endif
| {{ __('site.booking.group_type') }} | {{ __('site.group_types.'.$booking->group_type) }} |
@foreach($booking->addons ?? [] as $addon)
| + {{ localized($addon['name']) }} | {{ rupiah($addon['subtotal']) }} |
@endforeach
| **{{ __('site.booking.total') }}** | **{{ rupiah($booking->total) }}** |
</x-mail::table>

@if($booking->notes)
**{{ __('site.booking.notes') }}:** {{ $booking->notes }}
@endif

@if($forAdmin)
<x-mail::button :url="url('/admin/bookings/'.$booking->id.'/edit')">
{{ __('site.mail.open_admin') }}
</x-mail::button>

<x-mail::button :url="wa_link($booking->phone, $booking->adminWhatsappMessage())" color="success">
WhatsApp {{ $booking->customer_name }}
</x-mail::button>
@else
<x-mail::button :url="wa_link(null, $booking->customerWhatsappMessage())" color="success">
{{ __('site.booking.continue_wa') }}
</x-mail::button>

{{ __('site.booking.estimate_note') }}
@endif

{{ __('site.mail.regards') }}<br>
{{ setting('site_name', config('app.name')) }}
</x-mail::message>
