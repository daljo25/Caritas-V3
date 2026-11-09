<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\AttendanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewPdfAction::make()
                ->label('Certificado de Asistencia')
                ->color('success')
                ->icon('tabler-eye')
                ->pdfView('pdf.attendance')
                ->pdfData(fn (Model $attendance): array => ['attendance' => $attendance])
                ->pdfFilename(fn (Model $attendance): string => "certificado-asistencia-{$attendance->certificate_number}"),
            DeleteAction::make(),
        ];
    }
}
