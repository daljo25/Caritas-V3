<?php

namespace App\Filament\Imports;

use App\Models\GiftCard;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class GiftCardImporter extends Importer
{
    protected static ?string $model = GiftCard::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('serie')
                ->label('Serie')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('number')
                ->label('Número')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('pin')
                ->label('PIN')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('amount')
                ->label('Monto')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),
            ImportColumn::make('aid')
                ->label('Ayuda')
                ->relationship(),
            ImportColumn::make('issuer')
                ->label('Emisor')
                ->rules(['max:255', 'nullable']),
            ImportColumn::make('exp')
                ->label('Expiración')
                ->rules(['max:20', 'nullable']),
            ImportColumn::make('delivery_date')
                ->label('Fecha de entrega')
                ->rules(['date', 'nullable']),
        ];
    }

    public function resolveRecord(): ?GiftCard
    {
        // return GiftCard::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new GiftCard();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'La importación de las tarjetas de regalo se ha completado y se han importado ' . number_format($import->successful_rows) . ' ' . str('fila')->plural($import->successful_rows) . '.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' no se pudieron importar.';
        }

        return $body;
    }
}
