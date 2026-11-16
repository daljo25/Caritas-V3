<?php

use App\Filament\Resources\BeneficiaryResource\Pages\EditBeneficiary;
use App\Filament\Resources\BeneficiaryResource\RelationManagers\FamilyRelationManager;
use App\Filament\Resources\BeneficiaryResource\RelationManagers\RecordRelationManager;
use App\Models\Beneficiary;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(new User);

    $this->beneficiary = Beneficiary::first();

    if (! $this->beneficiary) {
        $this->markTestSkipped('No hay beneficiarios con los que abrir el gestor de relaciones.');
    }
});

it('no cierra el modal de familiares al pulsar fuera', function () {
    $actions = Livewire::test(FamilyRelationManager::class, [
        'ownerRecord' => $this->beneficiary,
        'pageClass' => EditBeneficiary::class,
    ])->instance()->getTable()->getFlatActions();

    // Un clic fuera del modal no debe descartar lo que se estaba escribiendo.
    expect($actions['create']->isModalClosedByClickingAway())->toBeFalse()
        ->and($actions['edit']->isModalClosedByClickingAway())->toBeFalse();
});

it('no cierra el modal del historial al pulsar fuera', function () {
    $actions = Livewire::test(RecordRelationManager::class, [
        'ownerRecord' => $this->beneficiary,
        'pageClass' => EditBeneficiary::class,
    ])->instance()->getTable()->getFlatActions();

    expect($actions['create']->isModalClosedByClickingAway())->toBeFalse()
        ->and($actions['edit']->isModalClosedByClickingAway())->toBeFalse();
});
