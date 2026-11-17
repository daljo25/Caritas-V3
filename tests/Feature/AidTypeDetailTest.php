<?php

use App\Filament\Resources\AidResource\Pages\CreateAid;
use App\Models\Aid;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(new User);

    $this->beneficiary = Beneficiary::query()->create(['name' => 'Titular de prueba']);
});

it('muestra el detalle solo cuando se elige una opción de otras necesidades', function () {
    Livewire::test(CreateAid::class)
        ->assertFormFieldHidden('type_detail')
        ->fillForm(['type' => 'Pago de suministro'])
        ->assertFormFieldHidden('type_detail')
        ->fillForm(['type' => 'Otras necesidades básicas de vivienda'])
        ->assertFormFieldVisible('type_detail')
        ->fillForm(['type' => 'Otras necesidades básicas'])
        ->assertFormFieldVisible('type_detail')
        ->fillForm(['type' => 'Otras necesidades básicas', 'type_detail' => 'Ropa de cama'])
        ->assertFormSet(['type_detail' => 'Ropa de cama'])
        ->fillForm(['type' => 'Pago de suministro'])
        ->assertFormSet(['type_detail' => null]);
});

it('añade el detalle entre paréntesis al concepto marcado en el recibí', function () {
    $aid = Aid::query()->create([
        'type' => 'Otras necesidades básicas de vivienda',
        'type_detail' => 'Ropa de cama',
        'beneficiary_id' => $this->beneficiary->id,
    ]);

    $html = view('pdf.receipt', ['record' => $aid])->render();

    expect($html)->toContain('Otras necesidades básicas de vivienda (Ropa de cama)');
});

it('no añade paréntesis cuando no hay detalle', function () {
    $aid = Aid::query()->create([
        'type' => 'Otras necesidades básicas de salud',
        'beneficiary_id' => $this->beneficiary->id,
    ]);

    $html = view('pdf.receipt', ['record' => $aid])->render();

    expect($html)->toContain('Otras necesidades básicas de salud</td>')
        ->and($html)->not->toContain('Otras necesidades básicas de salud (');
});

it('ignora el detalle en conceptos que no son otras necesidades', function () {
    $aid = Aid::query()->create([
        'type' => 'Pago de suministro',
        'type_detail' => 'Ropa de cama',
        'beneficiary_id' => $this->beneficiary->id,
    ]);

    $html = view('pdf.receipt', ['record' => $aid])->render();

    expect($html)->not->toContain('(Ropa de cama)');
});
