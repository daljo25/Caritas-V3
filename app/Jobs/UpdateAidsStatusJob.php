<?php

namespace App\Jobs;

use App\Models\Aid;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateAidsStatusJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): array
    {
        // Actualizar de 'Aceptada' a 'Terminada' si han pasado el tiempo transcurrido
        $Ayudas = Aid::where('status', 'Aceptada')
            ->where('end_date', '<', Carbon::now())
            ->update(['status' => 'Terminada']);

        return ['ayudas' => $Ayudas];
    }
}
