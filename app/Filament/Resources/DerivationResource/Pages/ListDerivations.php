<?php

namespace App\Filament\Resources\DerivationResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\DerivationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDerivations extends ListRecords
{
    protected static string $resource = DerivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
