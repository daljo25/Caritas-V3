<?php

namespace App\Console\Commands;

use App\Jobs\UpdateBeneficiariesStateJob;
use Illuminate\Console\Command;

class UpdateBeneficiariesState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beneficiaries:update-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar el estado de los beneficiarios según el tiempo transcurrido';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateBeneficiariesStateJob::dispatchSync();
        $this->info('Estados de beneficiarios actualizados con éxito.');
    }
}
