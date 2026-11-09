<?php

namespace App\Filament\Resources\AidResource\Pages;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\AidResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAid extends EditRecord
{
    protected static string $resource = AidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewPdfAction::make()
                ->label('Recibi')
                ->color('success')
                ->icon('tabler-eye')
                ->pdfView('pdf.receipt')
                ->pdfFilename(fn (Model $record): string => 'Ayuda de '.$record->type.' a '.$record->Beneficiary->name),
            DeleteAction::make(),
        ];
    }
}
