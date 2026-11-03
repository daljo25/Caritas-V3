<?php

use App\Filament\Widgets\GiftCardStats;
use App\Models\GiftCard;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('agrega las tarjetas por trimestre igual que antes', function () {
    $year = now()->year;

    $cards = [
        // Recibida en Q1, sin entregar
        ['created_at' => "$year-01-15", 'delivery_date' => null, 'amount' => 10],
        // Recibida en Q1, entregada en Q2
        ['created_at' => "$year-02-10", 'delivery_date' => "$year-05-20", 'amount' => 20],
        // Recibida en Q2, entregada en Q2
        ['created_at' => "$year-06-30", 'delivery_date' => "$year-06-01", 'amount' => 30],
        // Recibida el año pasado, entregada en Q3 de este
        ['created_at' => ($year - 1).'-12-31', 'delivery_date' => "$year-08-01", 'amount' => 40],
    ];

    foreach ($cards as $card) {
        GiftCard::query()->create($card + ['serie' => 'S', 'number' => '1', 'pin' => '1']);
    }

    $widget = new GiftCardStats;
    $stats = (fn () => $this->getStats())->call($widget);

    // Recibidas: Q1 = 2 (10 + 20), Q2 = 1 (30), Q3 = 0, Q4 = 0
    expect($stats[2]->getValue())->toBe('2 | 1 | 0 | 0')
        ->and($stats[2]->getDescription())->toBe('€ 30.00 | € 30.00 | € 0.00 | € 0.00');

    // Por entregar: Q1 = 1 (10), Q2 = 0, Q3 = 0, Q4 = 0
    expect($stats[0]->getValue())->toBe('1 | 0 | 0 | 0')
        ->and($stats[0]->getDescription())->toBe('€ 10.00 | € 0.00 | € 0.00 | € 0.00');

    // Entregadas: Q2 = 2 (20 + 30), Q3 = 1 (40)
    expect($stats[1]->getValue())->toBe('0 | 2 | 1 | 0')
        ->and($stats[1]->getDescription())->toBe('€ 0.00 | € 50.00 | € 40.00 | € 0.00');
});
