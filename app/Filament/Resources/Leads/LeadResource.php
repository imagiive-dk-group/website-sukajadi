<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Messages\MessageResource;
use App\Filament\Support\Fields;
use App\Models\Lead;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string|UnitEnum|null $navigationGroup = 'Prospek & Monitoring';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Prospek (Leads)';

    protected static ?string $modelLabel = 'prospek';

    protected static ?string $pluralModelLabel = 'prospek';

    protected static ?string $recordTitleAttribute = 'name';

    /** Status yang masih perlu ditindaklanjuti. */
    public const OPEN_STATUSES = ['new', 'contacted', 'negotiating'];

    public static function getNavigationBadge(): ?string
    {
        $count = Lead::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Prospek baru belum dihubungi';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email', 'interest'];
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return Fields::options('lead_statuses', 'site.lead_statuses');
    }

    /** @return array<string, string> */
    public static function sourceOptions(): array
    {
        return Fields::options('lead_sources', 'site.lead_sources');
    }

    /** @return array<int, string> */
    public static function userOptions(): array
    {
        return User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
    }

    public static function whatsappMessage(Lead $lead): string
    {
        $interest = filled($lead->interest) ? $lead->interest : null;

        if ($lead->locale === 'en') {
            return 'Hello '.$lead->name.', this is the Sukajadi Tourism Village team. Thank you for your interest'
                .($interest ? ' in '.$interest : '').'. How can we help you?';
        }

        return 'Halo '.$lead->name.', kami dari Desa Wisata Sukajadi. Terima kasih atas minat Anda'
            .($interest ? ' pada '.$interest : '').'. Ada yang bisa kami bantu?';
    }

    public static function isOverdue(Lead $lead): bool
    {
        return $lead->follow_up_at !== null
            && $lead->follow_up_at->lt(today())
            && in_array($lead->status, self::OPEN_STATUSES, true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data prospek')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('No. WhatsApp')
                            ->tel()
                            ->maxLength(30),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Select::make('locale')
                            ->label('Bahasa')
                            ->options(Fields::LOCALES)
                            ->default('id')
                            ->required(),
                        Select::make('source')
                            ->label('Sumber')
                            ->options(static::sourceOptions())
                            ->default('manual')
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options(static::statusOptions())
                            ->default('new')
                            ->required(),
                        TextInput::make('interest')
                            ->label('Minat')
                            ->placeholder('Misal: Paket edukasi kelinci untuk 40 siswa, vila 2 malam')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('package_id')
                            ->label('Paket terkait')
                            ->options(fn (): array => BookingResource::packageOptions())
                            ->searchable(),
                        Select::make('partner_id')
                            ->label('Mitra terkait')
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('estimated_value')
                            ->label('Perkiraan nilai')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0),
                    ]),

                Section::make('Tindak lanjut')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Ditangani oleh')
                            ->options(fn (): array => static::userOptions())
                            ->searchable(),
                        DatePicker::make('follow_up_at')
                            ->label('Jadwal follow-up'),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->helperText('Riwayat percakapan, permintaan khusus, alasan batal, dll.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Terhubung dengan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->visibleOn('edit')
                    ->schema([
                        TextEntry::make('booking_link')
                            ->label('Booking')
                            ->state(fn (?Lead $record): ?string => $record?->booking
                                ? $record->booking->code.' · '.__('site.status.'.$record->booking->status)
                                : null)
                            ->url(fn (?Lead $record): ?string => $record?->booking
                                ? BookingResource::getUrl('view', ['record' => $record->booking])
                                : null)
                            ->color('primary')
                            ->placeholder('Belum ada booking'),
                        TextEntry::make('message_link')
                            ->label('Pesan kontak')
                            ->state(fn (?Lead $record): ?string => $record?->message
                                ? 'Pesan dari '.$record->message->name.' ('.$record->message->created_at?->translatedFormat('j M Y').')'
                                : null)
                            ->url(fn (?Lead $record): ?string => $record?->message
                                ? MessageResource::getUrl('view', ['record' => $record->message])
                                : null)
                            ->color('primary')
                            ->placeholder('-'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['assignee', 'booking', 'message']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn (Lead $record): ?string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => __('site.lead_sources.'.$state)),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => __('site.lead_statuses.'.$state))
                    ->color(fn (?string $state): string => Fields::leadStatusColor($state))
                    ->sortable(),
                TextColumn::make('interest')
                    ->label('Minat')
                    ->wrap()
                    ->limit(50)
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('estimated_value')
                    ->label('Perkiraan nilai')
                    ->formatStateUsing(fn ($state): string => rupiah($state))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Ditangani')
                    ->placeholder('Belum ada'),
                TextColumn::make('follow_up_at')
                    ->label('Follow-up')
                    ->date('j M Y')
                    ->sortable()
                    ->placeholder('-')
                    ->color(fn (Lead $record): ?string => static::isOverdue($record)
                        ? 'danger'
                        : ($record->follow_up_at?->isToday() && in_array($record->status, self::OPEN_STATUSES, true) ? 'warning' : null))
                    ->weight(fn (Lead $record): ?string => static::isOverdue($record) ? 'bold' : null)
                    ->tooltip(fn (Lead $record): ?string => static::isOverdue($record) ? 'Terlambat di-follow-up' : null),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('j M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('Sumber')
                    ->options(static::sourceOptions()),
                SelectFilter::make('assigned_to')
                    ->label('Ditangani oleh')
                    ->options(fn (): array => static::userOptions()),
                Filter::make('mine')
                    ->label('Hanya prospek saya')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('assigned_to', auth()->id())),
                Filter::make('unassigned')
                    ->label('Belum ada yang menangani')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNull('assigned_to')),
            ])
            ->recordActions([
                static::whatsappAction(),
                ActionGroup::make([
                    static::claimAction(),
                    ...collect(['contacted', 'negotiating', 'won', 'lost'])
                        ->map(fn (string $status): Action => static::statusAction($status))
                        ->all(),
                    static::openBookingAction(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function whatsappAction(): Action
    {
        return Action::make('whatsapp')
            ->label('Kirim WA')
            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
            ->color('success')
            ->visible(fn (Lead $record): bool => filled($record->phone))
            ->url(fn (Lead $record): string => wa_link($record->phone, static::whatsappMessage($record)), shouldOpenInNewTab: true);
    }

    public static function statusAction(string $status): Action
    {
        $labels = [
            'contacted' => ['Tandai sudah dihubungi', Heroicon::OutlinedPhone],
            'negotiating' => ['Tandai negosiasi', Heroicon::OutlinedChatBubbleLeftRight],
            'won' => ['Tandai berhasil', Heroicon::OutlinedCheckCircle],
            'lost' => ['Tandai batal', Heroicon::OutlinedXCircle],
        ];

        return Action::make('status_'.$status)
            ->label($labels[$status][0])
            ->icon($labels[$status][1])
            ->color(Fields::leadStatusColor($status))
            ->visible(fn (Lead $record): bool => $record->status !== $status)
            ->action(function (Lead $record) use ($status): void {
                $record->update([
                    'status' => $status,
                    // Prospek yang sudah selesai tidak perlu jadwal follow-up lagi.
                    'follow_up_at' => in_array($status, ['won', 'lost'], true) ? null : $record->follow_up_at,
                    'assigned_to' => $record->assigned_to ?? auth()->id(),
                ]);

                Notification::make()
                    ->title('Prospek '.$record->name.': '.__('site.lead_statuses.'.$status))
                    ->success()
                    ->send();
            });
    }

    public static function claimAction(): Action
    {
        return Action::make('claim')
            ->label('Ambil prospek ini')
            ->icon(Heroicon::OutlinedHandRaised)
            ->color('primary')
            ->visible(fn (Lead $record): bool => $record->assigned_to !== auth()->id())
            ->action(function (Lead $record): void {
                $record->update(['assigned_to' => auth()->id()]);

                Notification::make()->title('Prospek '.$record->name.' sekarang Anda tangani')->success()->send();
            });
    }

    public static function openBookingAction(): Action
    {
        return Action::make('openBooking')
            ->label('Lihat booking')
            ->icon(Heroicon::OutlinedClipboardDocumentList)
            ->color('gray')
            ->visible(fn (Lead $record): bool => $record->booking !== null)
            ->url(fn (Lead $record): ?string => $record->booking ? BookingResource::getUrl('view', ['record' => $record->booking]) : null);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
