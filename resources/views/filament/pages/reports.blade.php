@php
    $cell = 'padding:.5rem .75rem;border-bottom:1px solid rgba(127,127,127,.2);vertical-align:top';
    $num = $cell.';text-align:right;white-space:nowrap';
    $head = 'padding:.5rem .75rem;border-bottom:1px solid rgba(127,127,127,.35);text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;opacity:.75';
@endphp

<x-filament-panels::page>
    <x-filament::section>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end">
            <label style="display:flex;flex-direction:column;gap:.25rem;font-size:.875rem;font-weight:500">
                Tahun
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="year">
                        @foreach ($years as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
            <label style="display:flex;flex-direction:column;gap:.25rem;font-size:.875rem;font-weight:500">
                Periode ekspor CSV
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="period">
                        <option value="all">Setahun penuh</option>
                        @foreach ($monthOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </label>
            <p style="font-size:.8rem;opacity:.75;max-width:36rem;margin:0">
                Jumlah booking & pax tidak menghitung booking yang dibatalkan. Estimasi pendapatan = total booking berstatus
                <em>Dikonfirmasi</em> dan <em>Selesai</em> (pembayaran sebenarnya dicatat lewat WhatsApp).
            </p>
        </div>
    </x-filament::section>

    <x-filament::section :heading="'Ringkasan per bulan · '.$this->selectedYear()">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                <thead>
                    <tr>
                        <th style="{{ $head }}">Bulan</th>
                        <th style="{{ $head }};text-align:right">Booking</th>
                        <th style="{{ $head }};text-align:right">Pax</th>
                        <th style="{{ $head }};text-align:right">Estimasi pendapatan</th>
                        <th style="{{ $head }};text-align:right">Batal</th>
                        <th style="{{ $head }}">Per paket</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($months as $row)
                        <tr style="{{ $row['bookings'] || $row['cancelled'] ? '' : 'opacity:.55' }}">
                            <td style="{{ $cell }};font-weight:500">{{ $row['label'] }}</td>
                            <td style="{{ $num }}">{{ number_format($row['bookings'], 0, ',', '.') }}</td>
                            <td style="{{ $num }}">{{ number_format($row['pax'], 0, ',', '.') }}</td>
                            <td style="{{ $num }}">{{ rupiah($row['revenue']) }}</td>
                            <td style="{{ $num }}">{{ $row['cancelled'] ?: '-' }}</td>
                            <td style="{{ $cell }};font-size:.8rem">{{ $row['packages'] ? implode(' · ', $row['packages']) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight:700">
                        <td style="{{ $cell }}">Total</td>
                        <td style="{{ $num }}">{{ number_format($total['bookings'], 0, ',', '.') }}</td>
                        <td style="{{ $num }}">{{ number_format($total['pax'], 0, ',', '.') }}</td>
                        <td style="{{ $num }}">{{ rupiah($total['revenue']) }}</td>
                        <td style="{{ $num }}">{{ $total['cancelled'] ?: '-' }}</td>
                        <td style="{{ $cell }}"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-filament::section>

    <x-filament::section :heading="'Per paket · '.$this->selectedYear()">
        @if ($packages->isEmpty())
            <p style="font-size:.875rem;opacity:.75;margin:0">Belum ada booking pada tahun ini.</p>
        @else
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                    <thead>
                        <tr>
                            <th style="{{ $head }}">Paket</th>
                            <th style="{{ $head }};text-align:right">Booking</th>
                            <th style="{{ $head }};text-align:right">Pax</th>
                            <th style="{{ $head }};text-align:right">Estimasi pendapatan</th>
                            <th style="{{ $head }};text-align:right">Batal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $row)
                            <tr>
                                <td style="{{ $cell }};font-weight:500">{{ $row['name'] }}</td>
                                <td style="{{ $num }}">{{ number_format($row['bookings'], 0, ',', '.') }}</td>
                                <td style="{{ $num }}">{{ number_format($row['pax'], 0, ',', '.') }}</td>
                                <td style="{{ $num }}">{{ rupiah($row['revenue']) }}</td>
                                <td style="{{ $num }}">{{ $row['cancelled'] ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
