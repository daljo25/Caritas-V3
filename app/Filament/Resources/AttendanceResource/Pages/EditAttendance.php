<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pdf')
                ->label('Certificado de Asistencia')
                ->color('success')
                ->icon('tabler-printer')
                ->action(function (Model $attendance) {
                    return response()->streamDownload(function () use ($attendance) {
                        echo FacadePdf::loadHtml(
                            Blade::render('pdf.attendance', ['attendance' => $attendance])
                        )->stream();
                    }, "certificado-asistencia-{$attendance->certificate_number}.pdf");
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
