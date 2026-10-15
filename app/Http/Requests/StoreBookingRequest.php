<?php

namespace App\Http\Requests;

use App\Services\BookingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['package', 'custom', 'partner'])],
            'partner_offer_id' => ['exclude_unless:type,partner', 'required', Rule::exists('partner_offers', 'id')->where('is_active', true)],
            'nights' => ['exclude_unless:type,partner', 'nullable', 'integer', 'min:1', 'max:30'],
            'package_tier_id' => ['exclude_unless:type,package', 'required', Rule::exists('package_tiers', 'id')],
            'attraction_ids' => ['exclude_unless:type,custom', 'required', 'array', 'min:1'],
            'attraction_ids.*' => ['integer', Rule::exists('attractions', 'id')->where('is_active', true)->whereNotNull('custom_price')],
            'visit_date' => ['required', 'date_format:Y-m-d'],
            'pax' => ['required', 'integer', 'min:1', 'max:2000'],
            'addons' => ['exclude_if:type,partner', 'nullable', 'array'],
            'addons.*' => ['integer', Rule::exists('addons', 'id')->where('is_active', true)],
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9 +().-]{8,30}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'organization' => ['nullable', 'string', 'max:150'],
            'group_type' => ['required', Rule::in(config('sukajadi.group_types'))],
            'notes' => ['nullable', 'string', 'max:2000'],
            'agree' => ['accepted'],
            // Honeypot anti-spam: harus kosong.
            'website' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'package_tier_id' => __('site.booking.package'),
            'partner_offer_id' => __('site.booking.offer'),
            'nights' => __('site.booking.nights'),
            'attraction_ids' => __('site.booking.custom_items'),
            'visit_date' => __('site.booking.date'),
            'pax' => __('site.booking.pax'),
            'customer_name' => __('site.booking.name'),
            'phone' => __('site.booking.phone'),
            'email' => __('site.booking.email'),
            'organization' => __('site.booking.organization'),
            'group_type' => __('site.booking.group_type'),
            'notes' => __('site.booking.notes'),
            'agree' => __('site.booking.agree'),
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $service = app(BookingService::class);
                $quote = $service->quote($this->all());

                if ($quote['type'] === 'package' && (! $quote['package'] || ! $quote['package']->is_active)) {
                    $validator->errors()->add('package_tier_id', __('site.booking.errors.tier'));

                    return;
                }

                if ($quote['type'] === 'partner' && (! $quote['partner'] || ! $quote['partner']->is_active)) {
                    $validator->errors()->add('partner_offer_id', __('site.booking.errors.tier'));

                    return;
                }

                if ((int) $this->input('pax') < $quote['min_pax']) {
                    $validator->errors()->add('pax', __('site.booking.errors.min_pax', ['count' => $quote['min_pax']]));
                }

                $problem = $service->dateProblem($quote['package'], Carbon::parse($this->input('visit_date')), (int) $this->input('pax'), $quote['type'] === 'partner');
                if ($problem) {
                    $validator->errors()->add('visit_date', $problem);
                }
            },
        ];
    }
}
