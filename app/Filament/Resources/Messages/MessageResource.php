<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\ListMessages;
use App\Filament\Resources\Messages\Pages\ViewMessage;
use App\Models\Message;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Pesan Masuk';

    protected static ?string $modelLabel = 'pesan';

    protected static ?string $pluralModelLabel = 'pesan masuk';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $count = Message::query()->whereNull('read_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    /** Pesan masuk hanya bisa dibaca & dihapus, tidak dibuat/diubah dari admin. */
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        $action = $action instanceof BackedEnum ? (string) $action->value : ($action instanceof UnitEnum ? $action->name : $action);

        if (in_array($action, ['create', 'update', 'replicate'], true)) {
            return Response::deny();
        }

        return parent::getAuthorizationResponse($action, $record);
    }

    /** Kontak berupa email? */
    public static function isEmail(?string $contact): bool
    {
        return filter_var(trim((string) $contact), FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Kontak berupa nomor telepon/WA? */
    public static function isPhone(?string $contact): bool
    {
        $digits = preg_replace('/\D+/', '', (string) $contact);

        return ! static::isEmail($contact) && strlen($digits) >= 8 && preg_match('/^[\d\s+().-]+$/', trim((string) $contact));
    }

    public static function replyUrl(Message $record): ?string
    {
        if (static::isPhone($record->contact)) {
            return wa_link($record->contact, 'Halo '.$record->name.', terima kasih sudah menghubungi Desa Wisata Sukajadi. ');
        }

        if (static::isEmail($record->contact)) {
            return 'mailto:'.trim($record->contact).'?subject='.rawurlencode('Balasan dari Desa Wisata Sukajadi');
        }

        return null;
    }

    public static function replyAction(): Action
    {
        return Action::make('reply')
            ->label(fn (Message $record): string => static::isEmail($record->contact) ? 'Balas via email' : 'Balas via WA')
            ->icon(fn (Message $record) => static::isEmail($record->contact) ? Heroicon::OutlinedEnvelope : Heroicon::OutlinedChatBubbleLeftRight)
            ->color('success')
            ->visible(fn (Message $record): bool => static::replyUrl($record) !== null)
            ->url(fn (Message $record): ?string => static::replyUrl($record), shouldOpenInNewTab: true);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pesan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')->label('Nama'),
                        TextEntry::make('contact')->label('Kontak')->copyable(),
                        TextEntry::make('created_at')->label('Diterima')->dateTime('l, j F Y H:i'),
                        TextEntry::make('locale')->label('Bahasa')->badge(),
                        TextEntry::make('message')
                            ->label('Isi pesan')
                            ->columnSpanFull()
                            ->extraAttributes(['style' => 'white-space: pre-line']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->state(fn (Message $record): bool => $record->read_at === null)
                    ->boolean()
                    ->trueIcon(Heroicon::Envelope)
                    ->falseIcon(Heroicon::OutlinedEnvelopeOpen)
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->tooltip(fn (Message $record): string => $record->read_at ? 'Sudah dibaca' : 'Belum dibaca'),
                TextColumn::make('name')
                    ->label('Nama')
                    ->weight(fn (Message $record) => $record->read_at ? null : 'bold')
                    ->searchable(),
                TextColumn::make('contact')
                    ->label('Kontak')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Diterima')
                    ->dateTime('j M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status')
                    ->nullable()
                    ->trueLabel('Sudah dibaca')
                    ->falseLabel('Belum dibaca'),
            ])
            ->recordActions([
                ViewAction::make(),
                static::replyAction(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Tandai sudah dibaca')
                        ->icon(Heroicon::OutlinedEnvelopeOpen)
                        ->action(fn (Collection $records) => $records->each->update(['read_at' => now()]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMessages::route('/'),
            'view' => ViewMessage::route('/{record}'),
        ];
    }
}
