<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;

class CleanPdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Elimina de la caché de ficheros las previsualizaciones de PDF caducadas.
     *
     * @return array{deleted: int, bytes: int}
     */
    public function handle(): array
    {
        $store = config('filament-record-pdf.cache_store', 'file');
        $config = config("cache.stores.{$store}");

        if (($config['driver'] ?? null) !== 'file') {
            return ['deleted' => 0, 'bytes' => 0];
        }

        $path = $config['path'] ?? storage_path('framework/cache/data');

        if (! File::isDirectory($path)) {
            return ['deleted' => 0, 'bytes' => 0];
        }

        $now = time();
        $deleted = 0;
        $bytes = 0;

        foreach (File::allFiles($path) as $file) {
            $handle = @fopen($file->getPathname(), 'r');

            if ($handle === false) {
                continue;
            }

            $expire = fread($handle, 10);
            fclose($handle);

            if (! is_string($expire) || ! ctype_digit($expire) || (int) $expire >= $now) {
                continue;
            }

            $bytes += $file->getSize();

            if (@unlink($file->getPathname())) {
                $deleted++;
            }
        }

        return ['deleted' => $deleted, 'bytes' => $bytes];
    }
}
