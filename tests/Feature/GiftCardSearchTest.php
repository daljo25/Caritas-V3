<?php

use App\Filament\Resources\GiftCardResource\Pages\ListGiftCards;
use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;

uses(DatabaseTransactions::class);

it('busca tarjetas de regalo por su número', function () {
    $this->actingAs(new User);

    $number = 'ST-'.random_int(100000, 999999);

    $target = GiftCard::query()->create([
        'serie' => 'A', 'number' => $number, 'pin' => '1', 'amount' => 10,
    ]);
    $other = GiftCard::query()->create([
        'serie' => 'B', 'number' => 'OTRA-'.random_int(100000, 999999), 'pin' => '2', 'amount' => 10,
    ]);

    Livewire::test(ListGiftCards::class)
        ->searchTable($number)
        ->assertCanSeeTableRecords([$target])
        ->assertCanNotSeeTableRecords([$other]);
});
