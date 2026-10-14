<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Throwable;

class Images
{
    /** Lebar varian (px) yang dibuat untuk setiap gambar. */
    public const WIDTHS = ['sm' => 480, 'md' => 1024, 'lg' => 1920];

    public const DISK = 'uploads';

    public static function variantPath(string $path, string $size): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'].'/';

        return $dir.$info['filename'].'-'.$size.'.webp';
    }

    public static function generate(string $path): void
    {
        $disk = Storage::disk(self::DISK);
        if (! $disk->exists($path)) {
            return;
        }

        try {
            $manager = ImageManager::usingDriver(Driver::class);
            foreach (self::WIDTHS as $size => $width) {
                $encoded = $manager->decodePath($disk->path($path))
                    ->scaleDown(width: $width)
                    ->encode(new WebpEncoder(quality: 80));
                $disk->put(self::variantPath($path, $size), (string) $encoded);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    public static function delete(string $path): void
    {
        $disk = Storage::disk(self::DISK);
        $disk->delete($path);
        foreach (array_keys(self::WIDTHS) as $size) {
            $disk->delete(self::variantPath($path, $size));
        }
    }

    /**
     * URL untuk ukuran tertentu; memakai file asli jika varian belum ada.
     */
    public static function url(?string $path, string $size = 'md'): ?string
    {
        if (blank($path)) {
            return null;
        }

        $disk = Storage::disk(self::DISK);
        $variant = self::variantPath($path, $size);

        return $disk->url($disk->exists($variant) ? $variant : $path);
    }

    public static function srcset(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $disk = Storage::disk(self::DISK);
        $parts = [];
        foreach (self::WIDTHS as $size => $width) {
            $variant = self::variantPath($path, $size);
            if ($disk->exists($variant)) {
                $parts[] = $disk->url($variant).' '.$width.'w';
            }
        }

        return $parts ? implode(', ', $parts) : null;
    }
}
