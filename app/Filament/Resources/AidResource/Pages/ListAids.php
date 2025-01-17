<?php

namespace App\Filament\Resources\AidResource\Pages;

use App\Exports\AidExport;
use App\Filament\Resources\AidResource;
use App\Jobs\UpdateAidsStatusJob;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Blade;
use App\Models\Aid;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListAids extends ListRecords
{
    protected static string $resource = AidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actualizar estados manualmente
            Actions\Action::make('Actualizar')
                ->label('Actualizar Estados')
                ->color('danger')
                ->icon('tabler-refresh')
                ->requiresConfirmation()
                ->action(function () {
                    $results = (new UpdateAidsStatusJob())->handle();
                    $terminadas = $results['ayudas'];

                    Notification::make()
                        ->title('Estados Actualizados')
                        ->body("$terminadas ayudas terminadas.")
                        ->success()
                        ->send();
                }),
            //exportar en excel el listado de ayudas
            Actions\Action::make('Export')
                ->label('Listado de Ayudas')
                ->color('info')
                ->icon('tabler-file-type-xls')
                ->modalSubmitActionLabel('Exportar')
                ->modalFooterActionsAlignment('center')
                ->modalCancelAction(false)
                ->form([
                    Fieldset::make('Filtros')->columnSpan(2)->schema([
                        Select::make('type')
                            ->label('Tipo de Ayuda')
                            ->options([
                                Aid::query()->get()->pluck('type', 'type')->unique()->toArray()
                            ])
                            ->multiple(),
                        Select::make('status')
                            ->label('Etapa')
                            ->options([
                                Aid::query()->get()->pluck('status', 'status')->unique()->toArray()
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
                        ->placeholder('Lista de Ayudas')
                        ->suffix('.xlsx'),
                ])
                ->action(function (array $data) {
                    // Procesar los datos del formulario
                    $filters = [
                        'type' => $data['type'] ?? [],
                        'status' => $data['status'] ?? [],
                        'start_date' => $data['start_date'] ?? null,
                        'end_date' => $data['end_date'] ?? null,

                    ];

                    // Crear una instancia de AidExport con los filtros proporcionados
                    $export = new AidExport($filters);
                    $filename = $data['filename'] ?? 'Lista de Ayudas';
                    // Descargar el archivo de Excel
                    return Excel::download($export, $filename . '.xlsx');
                }),
            //generar pdf de la carta al COVIRAN
            Actions\Action::make('GiftCard')
                ->label('Listado de Tarjetas')
                ->color('success')
                ->icon('tabler-credit-card')
                ->action(function (Aid $aid) {
                    return response()->streamDownload(function () use ($aid) {
                        echo FacadePdf::loadHtml(
                            Blade::render('pdf.gift-cards', ['record' => $aid])
                        )->stream();
                    }, 'Listado de Tarjetas ' . date('m-Y') . '.pdf');
                }),
                /* //generar pdf de la carta al COVIRAN
            Actions\Action::make('COVIRAN')
            ->label('COVIRAN')
            ->color('success')
            ->icon('tabler-printer')
            ->action(function (Aid $aid) {
                return response()->streamDownload(function () use ($aid) {
                    echo FacadePdf::loadHtml(
                        Blade::render('pdf.coviran', ['record' => $aid])
                    )->stream();
                }, 'COVIRAN ' . date('m-Y') . '.pdf');
            }), */
            Actions\CreateAction::make(),
        ];
    }
}
