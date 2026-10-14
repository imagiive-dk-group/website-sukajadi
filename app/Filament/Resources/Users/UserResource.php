<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    public const ROLES = [
        User::ROLE_SUPER_ADMIN => 'Super Admin',
        User::ROLE_ADMIN => 'Admin',
    ];

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Pengguna Admin';

    protected static ?string $modelLabel = 'pengguna admin';

    protected static ?string $pluralModelLabel = 'pengguna admin';

    protected static ?string $recordTitleAttribute = 'name';

    protected static function isSuperAdmin(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    /** Pengguna yang sedang login. */
    protected static function isSelf(Model $record): bool
    {
        return $record->getKey() === auth()->id();
    }

    /**
     * Semua aksi pada menu Pengguna hanya untuk Super Admin,
     * dan Super Admin tidak dapat menghapus akunnya sendiri.
     */
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (! static::isSuperAdmin()) {
            return Response::deny('Menu ini hanya untuk Super Admin.');
        }

        $action = $action instanceof BackedEnum ? (string) $action->value : ($action instanceof UnitEnum ? $action->name : $action);

        if (in_array($action, ['deleteAny', 'forceDeleteAny'], true)) {
            return Response::deny();
        }

        if (in_array($action, ['delete', 'forceDelete'], true) && $record && static::isSelf($record)) {
            return Response::deny('Anda tidak dapat menghapus akun sendiri.');
        }

        return Response::allow();
    }

    public static function canViewAny(): bool
    {
        return static::isSuperAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Kata sandi')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Kosongkan bila tidak ingin mengganti kata sandi.' : 'Minimal 8 karakter.'),
                        Select::make('role')
                            ->label('Peran')
                            ->options(self::ROLES)
                            ->default(User::ROLE_ADMIN)
                            ->required()
                            ->disabled(fn (?Model $record): bool => $record !== null && static::isSelf($record))
                            ->helperText('Super Admin dapat mengelola pengguna & pengaturan; Admin mengelola konten dan booking.'),
                        Toggle::make('is_active')
                            ->label('Aktif (boleh login)')
                            ->default(true)
                            ->disabled(fn (?Model $record): bool => $record !== null && static::isSelf($record))
                            ->helperText(fn (?Model $record): ?string => $record !== null && static::isSelf($record) ? 'Anda tidak dapat menonaktifkan akun sendiri.' : null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn (User $record): ?string => static::isSelf($record) ? 'Akun Anda' : null)
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Peran')
                    ->formatStateUsing(fn (?string $state): string => self::ROLES[$state] ?? (string) $state)
                    ->badge()
                    ->color(fn (?string $state): string => $state === User::ROLE_SUPER_ADMIN ? 'primary' : 'gray'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('j M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
