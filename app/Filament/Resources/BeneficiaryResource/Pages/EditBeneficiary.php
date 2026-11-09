<?php

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\BeneficiaryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBeneficiary extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewPdfAction::make('intervention')
                ->label('Hoja de Intervención')
                ->color('success')
                ->icon('tabler-eye')
                ->pdfView('pdf.intervention')
                ->pdfFilename(fn (Model $record): string => 'Hoja de Intervención de '.$record->name),
            PreviewPdfAction::make('protection')
                ->label('Protección de Datos')
                ->color('info')
                ->icon('tabler-user-shield')
                ->pdfView('pdf.data-protection')
                ->pdfFilename(fn (Model $record): string => 'Hoja de Proteccion de Datos de '.$record->name),

            DeleteAction::make(),
        ];
    }
}
