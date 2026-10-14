<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Kirim email ke semua admin aktif (+ email notifikasi tambahan dari Pengaturan).
 * Kegagalan email tidak boleh menggagalkan booking, cukup dicatat di log.
 */
class Notifier
{
    public static function adminEmails(): array
    {
        return User::query()->where('is_active', true)->pluck('email')
            ->push(setting('notification_email'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public static function admins(Mailable $mailable): void
    {
        $emails = self::adminEmails();
        if ($emails === []) {
            return;
        }

        self::send(fn () => Mail::to($emails)->send($mailable));
    }

    public static function to(?string $email, Mailable $mailable): void
    {
        if (blank($email)) {
            return;
        }

        self::send(fn () => Mail::to($email)->send($mailable));
    }

    protected static function send(callable $callback): void
    {
        try {
            $callback();
        } catch (Throwable $e) {
            report($e);
        }
    }
}
