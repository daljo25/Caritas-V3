<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAidsStatusJob;
use Illuminate\Console\Command;

class UpdateAidsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aids:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar el estado de las ayudas cuando han pasado el tiempo transcurrido';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Actualizar de 'Aceptada' a 'Terminada' si han pasado el tiempo transcurrido
        UpdateAidsStatusJob::dispatchSync();
        $this->info("Ayudas actualizadas con éxito.");
    }
    
}
