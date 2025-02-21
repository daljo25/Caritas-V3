<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Filament\Imports\GiftCardImporter;
use App\Filament\Resources\GiftCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGiftCards extends ListRecords
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make('Import')
                ->label('Importar')
                ->icon('tabler-table-import')
                ->color('info')
                ->Importer(GiftCardImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
