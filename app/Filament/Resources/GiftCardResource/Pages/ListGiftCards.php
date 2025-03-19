<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Exports\GiftCardExport;
use App\Filament\Imports\GiftCardImporter;
use App\Filament\Resources\GiftCardResource;
use App\Filament\Widgets\GiftCardStats;
use App\Filament\Widgets\StatsOverview;
use App\Models\Aid;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListGiftCards extends ListRecords
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //exportar en excel el listado de tarjetas
            Actions\Action::make('Export')
                ->label('Listado de Tarjetas')
                ->color('success')
                ->icon('tabler-file-type-xls')
                ->modalSubmitActionLabel('Exportar')
                ->modalFooterActionsAlignment('center')
                ->modalCancelAction(false)
                ->form([
                    Fieldset::make('Filtros')->columnSpan(2)->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'entregada' => 'Entregada',
                                'no_entregada' => 'No Entragada',
                            ]),
                        DatePicker::make('start_date')
                            ->label('Fecha de Inicio')
                            ->displayFormat('d-m-Y'),
                        DatePicker::make('end_date')
                            ->label('Fecha de Fin')
                            ->displayFormat('d-m-Y'),
                    ]),
                    TextInput::make('filename')
                        ->label('Nombre del Archivo')
                        ->placeholder('Lista de Tarjetas')
                        ->suffix('.xlsx'),
                ])
                ->action(function (array $data) {
                    // Procesar los datos del formulario
                    $filters = [
                        'status' => $data['status'] ?? [],
                        'start_date' => $data['start_date'] ?? null,
                        'end_date' => $data['end_date'] ?? null,

                    ];

                    // Crear una instancia de AidExport con los filtros proporcionados
                    $export = new GiftCardExport($filters);
                    $filename = $data['filename'] ?? 'Lista de Tarjetas';
                    // Descargar el archivo de Excel
                    return Excel::download($export, $filename . '.xlsx');
                }),
            Actions\ImportAction::make('Import')
                ->label('Importar')
                ->icon('tabler-table-import')
                ->color('info')
                ->Importer(GiftCardImporter::class),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GiftCardStats::class,
        ];
    }
    public function getHeaderWidgetsColumns(): int | array
    {
        return 5;
    }
}
