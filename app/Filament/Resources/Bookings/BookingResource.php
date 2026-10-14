<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\ViewBooking;
use App\Filament\Support\Fields;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageTier;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use UnitEnum;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Booking';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Daftar Booking';

    protected static ?string $modelLabel = 'booking';

    protected static ?string $pluralModelLabel = 'booking';

    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationBadge(): ?string
    {
        $count = Booking::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking baru menunggu konfirmasi';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'customer_name', 'organization', 'phone'];
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return Fields::options('booking_statuses', 'site.status');
    }

    /** @return array<int, string> */
    public static function packageOptions(): array
    {
        return Package::query()->orderBy('sort')->get()
            ->mapWithKeys(fn (Package $package): array => [$package->id => (string) $package->tr('name', 'id')])
            ->all();
    }

    /** @return array<int, string> */
    public static function addonOptions(): array
    {
        return Addon::query()->active()->get()
            ->mapWithKeys(fn (Addon $addon): array => [
                $addon->id => $addon->tr('name', 'id').' ('.rupiah($addon->price).' '.($addon->unit === 'per_group' ? 'per rombongan' : 'per pax').')',
            ])
            ->all();
    }

    /**
     * Hitung harga booking paket (dipakai saat admin membuat booking manual).
     *
     * @param  array<int|string>  $addonIds
     * @return array{package_id: ?int, package_tier_id: ?int, package_name: string, price_per_pax: int, subtotal: int, addons: array, addons_total: int, total: int}
     */
    public static function quote(int|string|null $tierId, int $pax, array $addonIds = []): array
    {
        $tier = filled($tierId) ? PackageTier::query()->with('package')->find($tierId) : null;
        $package = $tier?->package;
        $pricePerPax = (int) ($tier?->price ?? 0);

        $packageName = $package
            ? $package->tr('name', 'id').($package->tiers()->count() > 1 ? ' — '.$tier->tr('name', 'id') : '')
            : '';

        $addons = Addon::query()->whereIn('id', $addonIds)->orderBy('sort')->get()
            ->map(fn (Addon $addon): array => [
                'id' => $addon->id,
                'name' => $addon->name,
                'unit' => $addon->unit,
                'price' => $addon->price,
                'subtotal' => $addon->totalFor($pax),
            ])->values()->all();

        $subtotal = $pricePerPax * $pax;
        $addonsTotal = (int) collect($addons)->sum('subtotal');

        return [
            'package_id' => $package?->id,
            'package_tier_id' => $tier?->id,
            'package_name' => $packageName,
            'price_per_pax' => $pricePerPax,
            'subtotal' => $subtotal,
            'addons' => $addons,
            'addons_total' => $addonsTotal,
            'total' => $subtotal + $addonsTotal,
        ];
    }

    /**
     * Hitung ulang subtotal & tambahan per pax bila jumlah peserta berubah.
     *
     * @return array{addons: array, subtotal: int, addons_total: int, total: int}
     */
    public static function recalculate(Booking $booking, int $pax): array
    {
        $addons = collect($booking->addons ?? [])
            ->map(function (array $addon) use ($pax): array {
                $price = (int) ($addon['price'] ?? 0);
                $addon['subtotal'] = ($addon['unit'] ?? 'per_pax') === 'per_group' ? $price : $price * $pax;

                return $addon;
            })
            ->values()
            ->all();

        $subtotal = (int) $booking->price_per_pax * $pax;
        $addonsTotal = (int) collect($addons)->sum('subtotal');

        return [
            'addons' => $addons,
            'subtotal' => $subtotal,
            'addons_total' => $addonsTotal,
            'total' => $subtotal + $addonsTotal,
        ];
    }

    /** Rincian biaya yang mudah dibaca (paket custom, tambahan, total). */
    public static function costSummary(Booking $booking): HtmlString
    {
        $row = fn (string $label, string $value, bool $bold = false): string => '<tr><td style="padding:2px 16px 2px 0">'.e($label).'</td>'
            .'<td style="padding:2px 0;text-align:right;white-space:nowrap'.($bold ? ';font-weight:700' : '').'">'.e($value).'</td></tr>';

        $html = '<table style="width:100%;max-width:36rem;border-collapse:collapse">';

        if ($booking->type === 'custom' && filled($booking->custom_items)) {
            $html .= '<tr><td colspan="2" style="padding:2px 0;font-weight:600">Aktivitas paket custom</td></tr>';
            foreach ($booking->custom_items as $item) {
                $html .= $row('• '.localized($item['name'] ?? null, 'id'), rupiah($item['price'] ?? 0).'/pax');
            }
        }

        $html .= $row('Harga per pax × '.$booking->pax.' pax', rupiah($booking->price_per_pax).' × '.$booking->pax.' = '.rupiah($booking->subtotal));

        foreach ($booking->addons ?? [] as $addon) {
            $unit = ($addon['unit'] ?? 'per_pax') === 'per_group' ? '1 rombongan' : $booking->pax.' pax';
            $html .= $row('+ '.localized($addon['name'] ?? null, 'id').' ('.rupiah($addon['price'] ?? 0).' × '.$unit.')', rupiah($addon['subtotal'] ?? 0));
        }

        if (filled($booking->addons)) {
            $html .= $row('Total tambahan', rupiah($booking->addons_total));
        }

        $html .= '<tr><td colspan="2" style="border-top:1px solid rgba(127,127,127,.35);padding-top:4px"></td></tr>';
        $html .= $row('Estimasi total', rupiah($booking->total), true);
        $html .= '</table>';

        return new HtmlString($html);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Paket & jadwal')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode booking')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                        TextInput::make('package_name')
                            ->label('Paket')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                        Select::make('package_id')
                            ->label('Paket')
                            ->options(fn (): array => static::packageOptions())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('package_tier_id', null))
                            ->visibleOn('create'),
                        Select::make('package_tier_id')
                            ->label('Tingkat harga')
                            ->options(fn (Get $get): array => PackageTier::query()
                                ->where('package_id', $get('package_id'))
                                ->orderBy('sort')
                                ->get()
                                ->mapWithKeys(fn (PackageTier $tier): array => [
                                    $tier->id => $tier->tr('name', 'id').' — '.rupiah($tier->price).'/pax (min. '.$tier->min_pax.' pax)',
                                ])
                                ->all())
                            ->required()
                            ->live()
                            ->visibleOn('create'),
                        DatePicker::make('visit_date')
                            ->label('Tanggal kunjungan')
                            ->required(),
                        TextInput::make('pax')
                            ->label('Jumlah peserta (pax)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->live(onBlur: true)
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Bila diubah, subtotal & total dihitung ulang dengan harga per pax yang sama.' : null),
                        Select::make('status')
                            ->label('Status')
                            ->options(static::statusOptions())
                            ->default('new')
                            ->required(),
                    ]),

                Section::make('Data pemesan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Nama pemesan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('No. WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('organization')
                            ->label('Asal sekolah / instansi / rombongan')
                            ->maxLength(255),
                        Select::make('group_type')
                            ->label('Jenis rombongan')
                            ->options(Fields::options('group_types', 'site.group_types'))
                            ->default('family')
                            ->required(),
                        Select::make('locale')
                            ->label('Bahasa pemesan')
                            ->helperText('Bahasa pesan WhatsApp ke pemesan.')
                            ->options(Fields::LOCALES)
                            ->default('id')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan dari pemesan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Rincian biaya')
                    ->description('Estimasi. Pembayaran & DP dibahas dengan pemesan melalui WhatsApp.')
                    ->columnSpanFull()
                    ->schema([
                        CheckboxList::make('addon_ids')
                            ->label('Tambahan')
                            ->options(fn (): array => static::addonOptions())
                            ->live()
                            ->columns(2)
                            ->visibleOn('create'),
                        TextEntry::make('estimate')
                            ->label('Estimasi total')
                            ->state(function (Get $get): string {
                                $quote = static::quote($get('package_tier_id'), (int) $get('pax'), (array) ($get('addon_ids') ?? []));

                                return $quote['package_name'] === ''
                                    ? 'Pilih paket & tingkat harga terlebih dahulu.'
                                    : rupiah($quote['price_per_pax']).' × '.(int) $get('pax').' pax'
                                        .($quote['addons_total'] ? ' + tambahan '.rupiah($quote['addons_total']) : '')
                                        .' = '.rupiah($quote['total']);
                            })
                            ->visibleOn('create'),
                        TextEntry::make('cost_summary')
                            ->hiddenLabel()
                            ->state(fn (?Booking $record): ?HtmlString => $record ? static::costSummary($record) : null)
                            ->html()
                            ->visibleOn('edit'),
                    ]),

                Section::make('Catatan internal')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('admin_notes')
                            ->label('Catatan internal admin')
                            ->helperText('Hanya terlihat oleh admin. Misal: sudah transfer DP, ganti tanggal, permintaan menu khusus.')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booking')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('code')->label('Kode')->copyable()->weight('bold'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => __('site.status.'.$state))
                            ->color(fn (?string $state): string => Fields::statusColor($state)),
                        TextEntry::make('created_at')->label('Masuk')->dateTime('j M Y H:i'),
                        TextEntry::make('package_name')->label('Paket')->columnSpan(2),
                        TextEntry::make('type')
                            ->label('Jenis')
                            ->formatStateUsing(fn (?string $state): string => $state === 'custom' ? 'Paket custom' : 'Paket'),
                        TextEntry::make('visit_date')->label('Tanggal kunjungan')->date('l, j F Y'),
                        TextEntry::make('pax')->label('Peserta')->suffix(' pax'),
                        TextEntry::make('confirmed_at')->label('Dikonfirmasi')->dateTime('j M Y H:i')->placeholder('-'),
                    ]),
                Section::make('Pemesan')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('customer_name')->label('Nama'),
                        TextEntry::make('phone')->label('WhatsApp')->copyable(),
                        TextEntry::make('email')->label('Email')->placeholder('-'),
                        TextEntry::make('organization')->label('Asal / instansi')->placeholder('-'),
                        TextEntry::make('group_type')
                            ->label('Jenis rombongan')
                            ->formatStateUsing(fn (?string $state): string => __('site.group_types.'.$state)),
                        TextEntry::make('locale')->label('Bahasa')->badge(),
                        TextEntry::make('notes')->label('Catatan pemesan')->placeholder('-')->columnSpanFull(),
                    ]),
                Section::make('Rincian biaya')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('cost_summary')
                            ->hiddenLabel()
                            ->state(fn (Booking $record): HtmlString => static::costSummary($record))
                            ->html(),
                    ]),
                Section::make('Catatan internal')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('admin_notes')->hiddenLabel()->placeholder('Belum ada catatan.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('visit_date', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('visit_date')
                    ->label('Tgl kunjungan')
                    ->date('D, j M Y')
                    ->sortable(),
                TextColumn::make('package_name')
                    ->label('Paket')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                TextColumn::make('pax')
                    ->label('Pax')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Pemesan')
                    ->description(fn (Booking $record): ?string => $record->organization)
                    ->searchable(['customer_name', 'organization']),
                TextColumn::make('organization')
                    ->label('Asal / instansi')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->label('Estimasi total')
                    ->formatStateUsing(fn ($state): string => rupiah($state))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __('site.status.'.$state))
                    ->color(fn (?string $state): string => Fields::statusColor($state)),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('j M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::statusOptions())
                    ->multiple(),
                SelectFilter::make('package_id')
                    ->label('Paket')
                    ->options(fn (): array => static::packageOptions()),
                Filter::make('visit_date')
                    ->label('Tanggal kunjungan')
                    ->schema([
                        DatePicker::make('from')->label('Kunjungan dari tanggal'),
                        DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('visit_date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('visit_date', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Dari '.Carbon::parse($data['from'])->translatedFormat('j M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai '.Carbon::parse($data['until'])->translatedFormat('j M Y');
                        }

                        return $indicators;
                    }),
                Filter::make('upcoming')
                    ->label('Hanya kunjungan mendatang')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereDate('visit_date', '>=', today())),
            ])
            ->recordActions([
                static::whatsappAction(),
                ActionGroup::make([
                    static::confirmAction(),
                    static::completeAction(),
                    static::cancelAction(),
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ]);
    }

    public static function whatsappAction(): Action
    {
        return Action::make('whatsapp')
            ->label('Kirim WA')
            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
            ->color('success')
            ->url(fn (Booking $record): string => wa_link($record->phone, $record->adminWhatsappMessage()), shouldOpenInNewTab: true);
    }

    public static function confirmAction(): Action
    {
        return Action::make('confirm')
            ->label('Konfirmasi')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('info')
            ->visible(fn (Booking $record): bool => $record->status === 'new')
            ->action(function (Booking $record): void {
                $record->update(['status' => 'confirmed', 'confirmed_at' => now()]);

                Notification::make()->title('Booking '.$record->code.' dikonfirmasi')->success()->send();
            });
    }

    public static function completeAction(): Action
    {
        return Action::make('complete')
            ->label('Selesai')
            ->icon(Heroicon::OutlinedFlag)
            ->color('success')
            ->visible(fn (Booking $record): bool => in_array($record->status, ['new', 'confirmed'], true))
            ->action(function (Booking $record): void {
                $record->update([
                    'status' => 'completed',
                    'confirmed_at' => $record->confirmed_at ?? now(),
                ]);

                Notification::make()->title('Booking '.$record->code.' ditandai selesai')->success()->send();
            });
    }

    public static function cancelAction(): Action
    {
        return Action::make('cancel')
            ->label('Batalkan')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Batalkan booking?')
            ->modalDescription('Booking yang dibatalkan tidak lagi memakai kuota harian.')
            ->modalSubmitActionLabel('Ya, batalkan')
            ->visible(fn (Booking $record): bool => in_array($record->status, ['new', 'confirmed'], true))
            ->action(function (Booking $record): void {
                $record->update(['status' => 'cancelled']);

                Notification::make()->title('Booking '.$record->code.' dibatalkan')->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'view' => ViewBooking::route('/{record}'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
