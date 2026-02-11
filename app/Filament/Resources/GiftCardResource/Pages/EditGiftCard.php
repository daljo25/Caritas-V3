<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Filament\Resources\GiftCardResource;
use App\Models\GiftCard;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGiftCard extends EditRecord
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        
        // Buscar el registro anterior (ID - 1)
        $previousRecord = GiftCard::where('id', '<', $record->id)
            ->orderBy('id', 'desc')
            ->first();
        
        // Buscar el siguiente registro (ID + 1)
        $nextRecord = GiftCard::where('id', '>', $record->id)
            ->orderBy('id', 'asc')
            ->first();

        return [
            // Botón anterior
            Actions\Action::make('previous')
                ->label('')
                ->icon('tabler-chevron-left')
                ->color('gray')
                ->tooltip('Anterior')
                ->disabled(!$previousRecord)
                ->action(function () use ($previousRecord) {
                    if ($previousRecord) {
                        redirect(GiftCardResource::getUrl('edit', ['record' => $previousRecord->id]));
                    }
                }),
            
            // Botón siguiente
            Actions\Action::make('next')
                ->label('')
                ->icon('tabler-chevron-right')
                ->color('gray')
                ->tooltip('Siguiente')
                ->disabled(!$nextRecord)
                ->action(function () use ($nextRecord) {
                    if ($nextRecord) {
                        redirect(GiftCardResource::getUrl('edit', ['record' => $nextRecord->id]));
                    }
                }),
            
            Actions\DeleteAction::make(),
        ];
    }
}
