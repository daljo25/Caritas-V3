<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Exports\BeneficiaryExport;
use App\Filament\Resources\BeneficiaryResource;
use App\Jobs\UpdateBeneficiariesStateJob;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

use function Pest\Laravel\get;

class ListBeneficiaries extends ListRecords
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //actualizar estado de los beneficiarios
            Action::make('Update')
                ->label('Actualizar Estados')
                ->color('danger')
                ->icon('tabler-refresh')
                ->requiresConfirmation()
                ->action(function () {
                    $results = (new UpdateBeneficiariesStateJob())->handle();
                    $pasivos = $results['pasivos'];
                    $archivados = $results['archivados'];

                    Notification::make()
                        ->title('Estados Actualizados')
                        ->body("$pasivos usuarios cambiados a pasivos y $archivados usuarios cambiados a archivados.")
                        ->success()
                        ->send();
                }),
            //listado en excel de usuario
            Action::make('Export')
                ->label('Listado de Usuarios')
                ->color('info')
                ->icon('tabler-file-type-xls')
                ->action(function () {
                    return Excel::download(new BeneficiaryExport(), 'Listado de Usuarios.xlsx');
                }),
            Actions\CreateAction::make(),
        ];
    }
}
