<?php

namespace App\Filament\Resources\VolunteerResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\VolunteerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVolunteers extends ManageRecords
{
    protected static string $resource = VolunteerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
