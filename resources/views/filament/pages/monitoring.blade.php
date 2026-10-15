@php
    $cell = 'padding:.5rem .75rem;border-bottom:1px solid rgba(127,127,127,.2);vertical-align:top';
    $num = $cell.';text-align:right;white-space:nowrap';
    $head = 'padding:.5rem .75rem;border-bottom:1px solid rgba(127,127,127,.35);text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;opacity:.75';
    $grid = 'display:grid;gap:1.5rem;grid-template-columns:repeat(auto-fit,minmax(min(100%,22rem),1fr))';
    $kpi = 'display:flex;flex-direction:column;gap:.15rem;padding:.75rem 1rem;border:1px solid rgba(127,127,127,.25);border-radius:.75rem;min-width:9rem;flex:1';
    $kpiLabel = 'font-size:.75rem;opacity:.7';
    $kpiValue = 'font-size:1.5rem;font-weight:700;line-height:1.2';
    $fmt = fn ($n) => number_format((int) $n, 0, ',', '.');
    $maxLead = max(1, collect($leads['statuses'])->max('count'));
    $maxSource = max(1, max($leads['sources'] ?: [0]));
    $maxWa = max(1, max($wa['byType'] ?: [0]));
    $bar = fn (int $value, int $max, string $color) => '<div style="height:.5rem;border-radius:999px;background:rgba(127,127,127,.15);overflow:hidden"><div style="height:100%;width:'.round($value / $max * 100, 1).'%;background:var(--'.$color.'-500);border-radius:999px"></div></div>';
@endphp

<x-filament-panels::page>
    <x-filament::section>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end">
            <label style="display:flex;flex-direction:column;gap:.25rem;font-size:.875rem;font-weight:500">
                Periode
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="period">
                        @foreach ($periods as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
            <p style="font-size:.8rem;opacity:.75;max-width:40rem;margin:0">
                {{ $this->periodLabel() }}. Prospek, klik WhatsApp, dan booking dihitung berdasarkan waktu masuk (bukan tanggal kunjungan).
                Komisi hanya dari booking mitra yang tidak dibatalkan.
            </p>
        </div>
    </x-filament::section>

    <div style="display:flex;flex-wrap:wrap;gap:1rem">
        <div style="{{ $kpi }}"><span style="{{ $kpiLabel }}">Prospek masuk</span><span style="{{ $kpiValue }}">{{ $fmt($leads['total']) }}</span></div>
        <div style="{{ $kpi }}"><span style="{{ $kpiLabel }}">Konversi (berhasil ÷ total)</span><span style="{{ $kpiValue }}">{{ number_format($leads['conversion'], 1, ',', '.') }}%</span></div>
        <div style="{{ $kpi }}"><span style="{{ $kpiLabel }}">Klik WhatsApp</span><span style="{{ $kpiValue }}">{{ $fmt($wa['total']) }}</span></div>
        <div style="{{ $kpi }}"><span style="{{ $kpiLabel }}">Komisi mitra</span><span style="{{ $kpiValue }}">{{ rupiah($commissionTotal['commission']) }}</span></div>
    </div>

    <div style="{{ $grid }}">
        <x-filament::section heading="Corong prospek" :description="$fmt($leads['won']).' dari '.$fmt($leads['total']).' prospek berhasil menjadi booking'">
            <div style="display:flex;flex-direction:column;gap:.75rem">
                @foreach ($leads['statuses'] as $row)
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.875rem;margin-bottom:.25rem">
                            <span>{{ $row['label'] }}</span>
                            <strong>{{ $fmt($row['count']) }}</strong>
                        </div>
                        {!! $bar($row['count'], $maxLead, $row['color']) !!}
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section heading="Prospek per sumber">
            <div style="display:flex;flex-direction:column;gap:.75rem">
                @foreach ($leads['sources'] as $label => $count)
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:.875rem;margin-bottom:.25rem">
                            <span>{{ $label }}</span>
                            <strong>{{ $fmt($count) }}</strong>
                        </div>
                        {!! $bar($count, $maxSource, 'info') !!}
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>

    <div style="{{ $grid }}">
        <x-filament::section heading="Klik WhatsApp per jenis" :description="'Total '.$fmt($wa['total']).' klik'">
            @if (empty($wa['byType']))
                <p style="font-size:.875rem;opacity:.75;margin:0">Belum ada klik WhatsApp pada periode ini.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:.75rem">
                    @foreach ($wa['byType'] as $label => $count)
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:.875rem;margin-bottom:.25rem">
                                <span>{{ $label }}</span>
                                <strong>{{ $fmt($count) }}</strong>
                            </div>
                            {!! $bar($count, $maxWa, 'success') !!}
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>

        <x-filament::section heading="10 konten paling banyak diklik WhatsApp">
            @if ($wa['top']->isEmpty())
                <p style="font-size:.875rem;opacity:.75;margin:0">Belum ada data.</p>
            @else
                <div style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                        <thead>
                            <tr>
                                <th style="{{ $head }}">Konten</th>
                                <th style="{{ $head }}">Jenis</th>
                                <th style="{{ $head }};text-align:right">Klik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wa['top'] as $row)
                                <tr>
                                    <td style="{{ $cell }};font-weight:500">{{ $row['name'] }}</td>
                                    <td style="{{ $cell }}">{{ $row['type'] }}</td>
                                    <td style="{{ $num }}">{{ $fmt($row['clicks']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>

    <x-filament::section heading="Booking & pendapatan per jenis" description="Estimasi pendapatan = total booking berstatus Dikonfirmasi dan Selesai.">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                <thead>
                    <tr>
                        <th style="{{ $head }}">Jenis</th>
                        <th style="{{ $head }};text-align:right">Booking</th>
                        <th style="{{ $head }};text-align:right">Pax / jumlah</th>
                        <th style="{{ $head }};text-align:right">Nilai booking</th>
                        <th style="{{ $head }};text-align:right">Estimasi pendapatan</th>
                        <th style="{{ $head }};text-align:right">Batal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $row)
                        <tr>
                            <td style="{{ $cell }};font-weight:500">{{ $row['label'] }}</td>
                            <td style="{{ $num }}">{{ $fmt($row['bookings']) }}</td>
                            <td style="{{ $num }}">{{ $fmt($row['pax']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($row['gross']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($row['revenue']) }}</td>
                            <td style="{{ $num }}">{{ $row['cancelled'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight:700">
                        <td style="{{ $cell }}">Total</td>
                        <td style="{{ $num }}">{{ $fmt(collect($sales)->sum('bookings')) }}</td>
                        <td style="{{ $num }}">{{ $fmt(collect($sales)->sum('pax')) }}</td>
                        <td style="{{ $num }}">{{ rupiah(collect($sales)->sum('gross')) }}</td>
                        <td style="{{ $num }}">{{ rupiah(collect($sales)->sum('revenue')) }}</td>
                        <td style="{{ $num }}">{{ collect($sales)->sum('cancelled') ?: '-' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-filament::section>

    <x-filament::section heading="Komisi per mitra">
        @if ($commission->isEmpty())
            <p style="font-size:.875rem;opacity:.75;margin:0">Belum ada booking mitra pada periode ini.</p>
        @else
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                    <thead>
                        <tr>
                            <th style="{{ $head }}">Mitra</th>
                            <th style="{{ $head }};text-align:right">Booking</th>
                            <th style="{{ $head }};text-align:right">Penjualan kotor</th>
                            <th style="{{ $head }};text-align:right">Komisi</th>
                            <th style="{{ $head }};text-align:right">Belum ditagih</th>
                            <th style="{{ $head }};text-align:right">Sudah ditagih</th>
                            <th style="{{ $head }};text-align:right">Sudah dibayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commission as $row)
                            <tr>
                                <td style="{{ $cell }};font-weight:500">
                                    {{ $row['name'] }}
                                    @if ($row['rate'] !== null)
                                        <span style="opacity:.6;font-weight:400">· {{ rtrim(rtrim(number_format((float) $row['rate'], 2, ',', '.'), '0'), ',') }}%</span>
                                    @endif
                                </td>
                                <td style="{{ $num }}">{{ $fmt($row['bookings']) }}</td>
                                <td style="{{ $num }}">{{ rupiah($row['gross']) }}</td>
                                <td style="{{ $num }};font-weight:600">{{ rupiah($row['commission']) }}</td>
                                <td style="{{ $num }};color:var(--warning-600)">{{ rupiah($row['pending']) }}</td>
                                <td style="{{ $num }}">{{ rupiah($row['invoiced']) }}</td>
                                <td style="{{ $num }};color:var(--success-600)">{{ rupiah($row['paid']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:700">
                            <td style="{{ $cell }}">Total</td>
                            <td style="{{ $num }}">{{ $fmt($commissionTotal['bookings']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($commissionTotal['gross']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($commissionTotal['commission']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($commissionTotal['pending']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($commissionTotal['invoiced']) }}</td>
                            <td style="{{ $num }}">{{ rupiah($commissionTotal['paid']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
