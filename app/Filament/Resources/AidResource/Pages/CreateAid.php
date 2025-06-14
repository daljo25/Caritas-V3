<?php

namespace App\Filament\Resources\AidResource\Pages;

use App\Filament\Resources\AidResource;
use App\Models\Record;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAid extends CreateRecord
{
    protected static string $resource = AidResource::class;

    // Crear record después de crear una ayuda
    protected function afterCreate(): void
    {
        $aid = $this->record;

        $message = '';
        $type = $aid->type;
        $amount = number_format($aid->approved_amount ?? 0, 2, ',', '');
        $status = $aid->status ?? 'sin estado';
        $notes = $aid->notes;

        if ($aid->start_date && $aid->end_date) {
            $startDate = Carbon::parse($aid->start_date);
            $endDate = Carbon::parse($aid->end_date);

            if ($startDate->equalTo($endDate)) {
                // 🎯 Ayuda puntual
                $message = "Ayuda de {$type} por {$amount} € ha sido {$status}.";
            } else {
                // 📅 Ayuda mensual
                $start = $startDate->copy()->startOfMonth();
                $end = $endDate->copy()->startOfMonth();

                $months = [];
                $current = $start->copy();
                while ($current <= $end) {
                    $months[] = Str::ucfirst($current->locale('es')->translatedFormat('F'));
                    $current->addMonth();
                }

                $message = sprintf(
                    'Ayuda de %s por %s € al mes ha sido %s por %d mes%s: %s.',
                    $type,
                    $amount,
                    $status,
                    count($months),
                    count($months) > 1 ? 'es' : '',
                    implode(', ', $months)
                );
            }
        } else {
            // ❔ Fechas no definidas
            $message = "Ayuda de {$type} por {$amount} € ha sido {$status}.";
        }

        // 📝 Agregar nota si existe
        if ($notes) {
            $message .= "\n\nNota: " . trim($notes);
        }

        Record::create([
            'date' => now()->toDateString(),
            'incident' => $message,
            'beneficiary_id' => $aid->beneficiary_id,
            'volunteer_id' => $aid->volunteer_id,
        ]);
    }
}
