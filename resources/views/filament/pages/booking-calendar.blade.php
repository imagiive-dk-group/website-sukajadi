<x-filament-panels::page>
    <div style="display:flex;flex-wrap:wrap;gap:.75rem 1.5rem;align-items:center;justify-content:space-between">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;align-items:center">
            @foreach ($statuses as $status)
                <x-filament::badge :color="$status['color']" size="sm">{{ $status['label'] }}</x-filament::badge>
            @endforeach
            <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;opacity:.8">
                <span style="display:inline-block;width:.9rem;height:.9rem;border-radius:.25rem;background:repeating-linear-gradient(45deg,rgba(127,127,127,.25) 0 4px,transparent 4px 8px);border:1px solid rgba(127,127,127,.4)"></span>
                Tanggal ditutup
            </span>
        </div>
        <div style="font-size:.875rem">
            Bulan ini: <strong>{{ $monthTotalBookings }}</strong> booking aktif ·
            <strong>{{ number_format($monthTotalPax, 0, ',', '.') }}</strong> pax
            <span style="opacity:.7">(tanpa yang dibatalkan)</span>
        </div>
    </div>

    <div style="overflow-x:auto">
        <div style="display:grid;grid-template-columns:repeat(7,minmax(7.5rem,1fr));gap:.375rem;min-width:56rem">
            @foreach ($weekdays as $weekday)
                <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;opacity:.7;padding:.25rem .375rem">
                    {{ $weekday }}
                </div>
            @endforeach

            @foreach ($days as $day)
                <div
                    wire:key="day-{{ $day['date']->format('Y-m-d') }}"
                    style="min-height:7.5rem;border-radius:.5rem;padding:.375rem;display:flex;flex-direction:column;gap:.25rem;
                        border:{{ $day['isToday'] ? '2px solid var(--primary-600)' : '1px solid rgba(127,127,127,.25)' }};
                        {{ $day['inMonth'] ? '' : 'opacity:.45;' }}
                        {{ count($day['blocked']) ? 'background:repeating-linear-gradient(45deg,rgba(127,127,127,.12) 0 6px,transparent 6px 12px);' : '' }}"
                >
                    <div style="display:flex;justify-content:space-between;align-items:baseline;gap:.25rem">
                        <span style="font-weight:600;font-size:.875rem">{{ $day['date']->day }}</span>
                        @if ($day['pax'] > 0)
                            <span style="font-size:.7rem;font-weight:600;opacity:.85" title="Total peserta (tanpa yang dibatalkan)">
                                {{ number_format($day['pax'], 0, ',', '.') }} pax
                            </span>
                        @endif
                    </div>

                    @foreach ($day['blocked'] as $reason)
                        <div style="font-size:.7rem;line-height:1.2;opacity:.75;font-style:italic">
                            Ditutup – {{ $reason }}
                        </div>
                    @endforeach

                    @foreach ($day['bookings'] as $booking)
                        <a
                            href="{{ $booking['url'] }}"
                            title="{{ $booking['code'] }} · {{ $booking['package'] }} · {{ $booking['pax'] }} pax · {{ $booking['customer'] }} ({{ $booking['statusLabel'] }})"
                            style="display:block;text-decoration:none;color:inherit;border-radius:.375rem;padding:.125rem 0;{{ $booking['status'] === 'cancelled' ? 'opacity:.55;text-decoration:line-through;' : '' }}"
                        >
                            <x-filament::badge :color="$booking['color']" size="sm">
                                {{ $booking['code'] }} · {{ $booking['pax'] }} pax
                            </x-filament::badge>
                            <div style="font-size:.7rem;line-height:1.2;margin-top:.125rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                {{ $booking['package'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
