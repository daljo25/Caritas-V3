<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Filament\Resources\GiftCardResource;
use App\Models\GiftCard;
use App\Models\Record;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditGiftCard extends EditRecord
{
    protected static string $resource = GiftCardResource::class;

    protected ?int $previousAidId = null;
    protected ?string $previousDeliveryDate = null;

    protected function beforeSave(): void
    {
        $this->previousAidId = $this->record->aid_id;
        $this->previousDeliveryDate = $this->record->delivery_date?->toDateString();
    }

    protected function afterSave(): void
    {
        $giftCard = $this->record->fresh(['aid']);

        // Registrar solo el paso de tarjeta pendiente a entregada, evitando
        // duplicados cuando se editen otros datos más adelante.
        $wasDelivered = $this->previousAidId !== null && $this->previousDeliveryDate !== null;
        if ($wasDelivered || !$giftCard->aid_id || !$giftCard->delivery_date) {
            return;
        }

        $aid = $giftCard->aid;
        if (!$aid) {
            return;
        }

        $deliveredAt = Carbon::parse($giftCard->updated_at);
        $aidMonth = $giftCard->delivery_date
            ? Str::ucfirst(Carbon::parse($giftCard->delivery_date)->locale('es')->translatedFormat('F'))
            : 'sin especificar';
        $message = sprintf(
            'Tarjeta #%s entregada el día %s correspondiente a la ayuda del mes de %s.',
            $giftCard->number,
            $deliveredAt->format('d/m/Y'),
            $aidMonth
        );

        Record::create([
            'date' => $deliveredAt->toDateString(),
            'incident' => $message,
            'beneficiary_id' => $aid->beneficiary_id,
            'volunteer_id' => 1,
        ]);
    }

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
