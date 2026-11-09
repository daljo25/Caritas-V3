<?php

use App\Filament\Resources\AidResource\Pages\ListAids;
use App\Filament\Resources\DonorResource\Pages\ListDonors;
use App\Models\User;
use Livewire\Livewire;

it('abre el visor de PDF para un listado sin registro', function () {
    $this->actingAs(new User);

    Livewire::test(ListAids::class)
        ->callAction('GiftCard', ['month' => '01', 'year' => 2026])
        ->assertActionMounted('previewGeneratedPdf');
});

it('abre el visor de PDF para las donaciones anuales', function () {
    $this->actingAs(new User);

    Livewire::test(ListDonors::class)
        ->callAction('pdf')
        ->assertActionMounted('previewGeneratedPdf');
});
