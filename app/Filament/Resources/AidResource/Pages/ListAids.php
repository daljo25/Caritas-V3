<?php

namespace App\Filament\Resources\AidResource\Pages;

use App\Exports\AidExport;
use App\Filament\Concerns\HasGeneratedPdfPreview;
use App\Filament\Resources\AidResource;
use App\Jobs\CleanPdfJob;
use App\Jobs\UpdateAidsStatusJob;
use App\Models\Aid;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Fieldset;
use Maatwebsite\Excel\Facades\Excel;

class ListAids extends ListRecords
{
    use HasGeneratedPdfPreview;

    protected static string $resource = AidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actualizar estados manualmente
            Actions\Action::make('Actualizar')
                ->label('Actualizar Estados')
                ->color('danger')
                ->icon('tabler-refresh')
                ->requiresConfirmation()
                ->action(function () {
                    $results = (new UpdateAidsStatusJob)->handle();
                    $terminadas = $results['ayudas'];

                    // Limpiar las previsualizaciones de PDF caducadas al actualizar estados.
                    (new CleanPdfJob)->handle();

                    Notification::make()
                        ->title('Estados Actualizados')
                        ->body("$terminadas ayudas terminadas.")
                        ->success()
                        ->send();
                }),
            // exportar en excel el listado de ayudas
            Actions\Action::make('Export')
                ->label('Listado de Ayudas')
                ->color('info')
                ->icon('tabler-file-type-xls')
                ->modalSubmitActionLabel('Exportar')
                ->modalFooterActionsAlignment('center')
                ->modalCancelAction(false)
                ->schema([
                    Fieldset::make('Filtros')->columnSpan(2)->schema([
                        Select::make('type')
                            ->label('Tipo de Ayuda')
                            ->options([
                                Aid::query()->get()->pluck('type', 'type')->unique()->toArray(),
                            ])
                            ->multiple(),
                        Select::make('status')
                            ->label('Etapa')
                            ->options([
                                Aid::query()->get()->pluck('status', 'status')->unique()->toArray(),
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
                    return Excel::download($export, $filename.'.xlsx');
                }),
            // generar pdf de la carta al COVIRAN
            Actions\Action::make('GiftCard')
                ->label('Listado de Tarjetas')
                ->color('success')
                ->icon('tabler-credit-card')
                ->modalHeading('Generar Listado de Tarjetas')
                ->modalSubmitActionLabel('Generar PDF')
                ->modalFooterActionsAlignment('center')
                ->modalCancelAction(false)
                ->schema([
                    Select::make('month')
                        ->label('Mes')
                        ->options([
                            '01' => 'Enero',
                            '02' => 'Febrero',
                            '03' => 'Marzo',
                            '04' => 'Abril',
                            '05' => 'Mayo',
                            '06' => 'Junio',
                            '07' => 'Julio',
                            '08' => 'Agosto',
                            '09' => 'Septiembre',
                            '10' => 'Octubre',
                            '11' => 'Noviembre',
                            '12' => 'Diciembre',
                        ])
                        ->default(Carbon::now()->format('m'))
                        ->required(),
                    Select::make('year')
                        ->label('Año')
                        ->options(function () {
                            $years = [];
                            $currentYear = Carbon::now()->year;
                            for ($i = $currentYear - 5; $i <= $currentYear; $i++) {
                                $years[$i] = $i;
                            }

                            return $years;
                        })
                        ->default(Carbon::now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $month = $data['month'];
                    $year = $data['year'];

                    $pdfContent = view('pdf.gift-cards', [
                        'month' => $month,
                        'year' => $year,
                    ])->render();

                    $this->openPdfPreview($pdfContent, 'Listado de Tarjetas '.$month.'-'.$year);
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
            CreateAction::make(),
        ];
    }
}
