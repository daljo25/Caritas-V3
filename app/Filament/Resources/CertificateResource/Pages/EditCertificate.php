<?php

namespace App\Filament\Resources\CertificateResource\Pages;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\CertificateResource;
use App\Models\Certificate;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCertificate extends EditRecord
{
    protected static string $resource = CertificateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Certificate::withResolvedRecipient($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            PreviewPdfAction::make()
                ->label('Imprimir')
                ->icon('tabler-printer')
                ->pdfView('pdf.certificate')
                ->pdfFilename(fn (Certificate $record): string => $record->title.' de '.$record->recipient_name),
            DeleteAction::make(),
        ];
    }
}
