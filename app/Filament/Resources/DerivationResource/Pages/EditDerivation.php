<?php

namespace App\Filament\Resources\DerivationResource\Pages;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\DerivationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDerivation extends EditRecord
{
    protected static string $resource = DerivationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewPdfAction::make()
                ->label('Hoja de Derivación')
                ->color('success')
                ->icon('tabler-eye')
                ->pdfView('pdf.derivation')
                ->pdfFilename(fn (Model $record): string => 'Hoja de Derivación de '.$record->Beneficiary->name),
            DeleteAction::make(),
        ];
    }
}
