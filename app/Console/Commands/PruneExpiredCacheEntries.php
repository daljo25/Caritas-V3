<?php

namespace App\Console\Commands;

use App\Jobs\CleanPdfJob;
use Illuminate\Console\Command;

class PruneExpiredCacheEntries extends Command
{
    protected $signature = 'cache:prune-expired';

    protected $description = 'Elimina las entradas caducadas del almacén de caché en ficheros (previsualizaciones de PDF, etc.)';

    public function handle(): int
    {
        $result = (new CleanPdfJob)->handle();

        $this->info(sprintf(
            'Entradas caducadas eliminadas: %d (%s KB).',
            $result['deleted'],
            number_format($result['bytes'] / 1024, 1, ',', '.'),
        ));

        return self::SUCCESS;
    }
}
