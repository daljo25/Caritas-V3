<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use App\Exports\MonthlyReportExport;
use App\Filament\Resources\MonthlyReportResource;
use App\Models\MonthlyReport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class ListMonthlyReports extends ListRecords
{
    protected static string $resource = MonthlyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Listado de Reportes')
                ->color('info')
                ->icon('tabler-file-type-xls')
                ->modalSubmitActionLabel('Exportar')
                ->modalFooterActionsAlignment('center')
                ->modalCancelAction(false)
                ->form([
                    Fieldset::make('Filtros')->columnSpan(2)->schema([
                        Select::make('year')
                            ->label('Año')
                            ->multiple()
                            ->options(MonthlyReport::query()->get()->pluck('year', 'year')->unique()->toArray()),
                    ]),
                    TextInput::make('filename')
                        ->label('Nombre del Archivo')
                        ->placeholder('Lista de Reportes')
                        ->suffix('.xlsx'),
                ])
                ->action(function (array $data) {
                    // Procesar los datos del formulario
                    $filters = [
                        'year' => $data['year'] ?? null,
                    ];

                    // Crear una instancia de DonationsExport con los filtros proporcionados
                    $export = new MonthlyReportExport($filters);
                    $filename = $data['filename'] ?? 'Lista de Reportes';
                    // Descargar el archivo de Excel
                    return FacadesExcel::download($export, $filename . '.xlsx');
                }),
            Actions\CreateAction::make(),
        ];
    }
}
