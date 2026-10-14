<?php

namespace App\Models\Concerns;

use App\Support\Images;

/**
 * Membuat versi WebP (kecil/sedang/besar) setiap kali gambar diunggah,
 * dan menghapus file lama ketika gambar diganti atau data dihapus.
 */
trait HasImageVariants
{
    public static function bootHasImageVariants(): void
    {
        static::saved(function ($model) {
            foreach ($model->imageColumns() as $column) {
                if (! $model->wasChanged($column) && ! $model->wasRecentlyCreated) {
                    continue;
                }

                $old = $model->getOriginal($column);
                if ($model->wasChanged($column) && filled($old) && $old !== $model->{$column}) {
                    Images::delete($old);
                }

                if (filled($model->{$column})) {
                    Images::generate($model->{$column});
                }
            }
        });

        static::deleted(function ($model) {
            foreach ($model->imageColumns() as $column) {
                if (filled($model->{$column})) {
                    Images::delete($model->{$column});
                }
            }
        });
    }

    public function imageColumns(): array
    {
        return $this->imageColumns ?? [];
    }
}
