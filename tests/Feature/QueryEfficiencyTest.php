<?php

use App\Filament\Resources\AidResource\Pages\ListAids;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

it('carga los beneficiarios del listado con una sola consulta', function () {
    $this->actingAs(new User);

    DB::flushQueryLog();
    DB::enableQueryLog();

    Livewire::test(ListAids::class)->assertOk();

    // Una consulta para toda la pagina, no una por ayuda.
    $beneficiaries = collect(DB::getQueryLog())->pluck('query')
        ->filter(fn (string $sql) => str_contains($sql, 'from "beneficiaries"'));

    expect($beneficiaries->count())->toBeLessThanOrEqual(1);
});

it('resuelve los ajustes desde una sola consulta por peticion', function () {
    DB::flushQueryLog();
    DB::enableQueryLog();

    expect(setting('parish.city'))->toBe('Sevilla')
        ->and(setting('parish'))->toBeArray()
        ->and(setting('app.name'))->toBe('Cáritas Bellavista')
        ->and(setting('no.existe', 'por defecto'))->toBe('por defecto');

    // Los PDF llaman a setting() varias veces y solo debe consultarse una.
    $settings = collect(DB::getQueryLog())->pluck('query')
        ->filter(fn (string $sql) => str_contains($sql, 'from "settings"'));

    expect($settings->count())->toBe(1);
});
