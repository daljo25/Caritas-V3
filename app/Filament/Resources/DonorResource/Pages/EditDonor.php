<?php

namespace App\Filament\Resources\DonorResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\DonorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDonor extends EditRecord
{
    protected static string $resource = DonorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
