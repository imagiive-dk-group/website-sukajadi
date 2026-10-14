<x-mail::message>
# {{ __('site.mail.message_subject', ['name' => $contactMessage->name]) }}

**{{ __('site.contact.contact') }}:** {{ $contactMessage->contact }}

{{ $contactMessage->message }}

<x-mail::button :url="url('/admin/messages')">
{{ __('site.mail.open_admin') }}
</x-mail::button>
</x-mail::message>
