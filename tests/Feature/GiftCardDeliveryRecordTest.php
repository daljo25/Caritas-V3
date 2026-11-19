<?php

use App\Filament\Resources\GiftCardResource\Pages\EditGiftCard;
use App\Models\Aid;
use App\Models\Beneficiary;
use App\Models\GiftCard;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;

uses(DatabaseTransactions::class);

it('incluye el monto de la tarjeta en el registro al entregarla a un beneficiario', function () {
    $this->actingAs(new User);

    $beneficiary = Beneficiary::query()->create(['name' => 'Beneficiario de prueba']);
    $aid = Aid::query()->create([
        'type' => 'Alimentación',
        'status' => 'Aceptada',
        'beneficiary_id' => $beneficiary->getKey(),
    ]);
    $card = GiftCard::query()->create([
        'serie' => 'A',
        'number' => 'TARJETA-123',
        'pin' => '1',
        'amount' => 50,
    ]);

    Livewire::test(EditGiftCard::class, ['record' => $card->getKey()])
        ->fillForm([
            'aid_id' => $aid->getKey(),
            'delivery_date' => now()->toDateString(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $record = Record::query()->latest('id')->first();

    expect($record)->not->toBeNull()
        ->and($record->beneficiary_id)->toBe($beneficiary->getKey())
        ->and($record->incident)->toContain('TARJETA-123', '50,00', '€');
});
