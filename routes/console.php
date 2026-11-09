<?php

use Illuminate\Support\Facades\Schedule;

// Cálculos mensuales de estados. Desde Laravel 11 el scheduler se declara aquí:
// `app/Console/Kernel.php` ya no se carga.
Schedule::command('aids:update-status')
    ->monthly()
    ->sendOutputTo(storage_path('logs/aids.log'));

Schedule::command('beneficiaries:update-state')
    ->monthly()
    ->sendOutputTo(storage_path('logs/beneficiaries.log'));

// Las previsualizaciones de PDF viven en la caché de ficheros solo mientras
// dura el visor; este barrido garantiza que las caducadas no se queden en disco.
Schedule::command('cache:prune-expired')
    ->everyMinute()
    ->withoutOverlapping();
