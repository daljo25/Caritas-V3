<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\MonthlyReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyReport extends EditRecord
{
    protected static string $resource = MonthlyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
