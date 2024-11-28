<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Exports\BeneficiaryExport;
use App\Filament\Resources\BeneficiaryResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListBeneficiaries extends ListRecords
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Export')
                ->label('Listado de Usuarios')
                ->color('info')
                ->icon('tabler-file-type-xls')
                ->action(function () {
                    return Excel::download(new BeneficiaryExport(), 'Listado de Usuarios.xlsx');
                })
                ,
            Actions\CreateAction::make(),
        ];
    }
}
