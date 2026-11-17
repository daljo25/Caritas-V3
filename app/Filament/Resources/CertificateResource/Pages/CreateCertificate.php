<?php

namespace App\Filament\Resources\CertificateResource\Pages;

use App\Filament\Resources\CertificateResource;
use App\Models\Certificate;
use Filament\Resources\Pages\CreateRecord;

class CreateCertificate extends CreateRecord
{
    protected static string $resource = CertificateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Certificate::withResolvedRecipient($data);
    }

    protected function getRedirectUrl(): string
    {
        // Recién creado es cuando se quiere imprimir: se abre la ficha.
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
