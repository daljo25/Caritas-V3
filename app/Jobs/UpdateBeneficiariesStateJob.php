<?php

namespace App\Jobs;

use App\Models\Beneficiary;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateBeneficiariesStateJob implements ShouldQueue
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
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        // Actualizar de 'Activo' a 'Pasivo'
        $pasivos = Beneficiary::where('state', 'Activo')
            ->where('updated_at', '<=', $threeMonthsAgo)
            ->update(['state' => 'Pasivo']);

        // Actualizar de 'Pasivo' a 'Archivado'
        $archivados = Beneficiary::where('state', 'Pasivo')
            ->where('updated_at', '<=', $threeMonthsAgo)
            ->update(['state' => 'Archivado']);

        // retorno de los resultados
        return ['pasivos' => $pasivos, 'archivados' => $archivados];
    }
}
