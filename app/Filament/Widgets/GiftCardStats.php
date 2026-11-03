<?php

namespace App\Filament\Widgets;

use App\Models\GiftCard;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GiftCardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $year = now()->year;

        // Una sola consulta para todo el año: antes se hacían 6 por trimestre
        // (24 en total) y lerd lo marcaba como N+1 en cada render del panel.
        $cards = GiftCard::query()
            ->where(fn ($query) => $query
                ->whereYear('created_at', $year)
                ->orWhereYear('delivery_date', $year))
            ->get(['created_at', 'delivery_date', 'amount']);

        // --- Tarjetas Recibidas ---
        $quarterDataTotal = [];
        $amountsTotal = [];

        // --- Tarjetas por Entregar ---
        $quarterDataPending = [];
        $amountsPending = [];

        // --- Tarjetas Entregadas ---
        $quarterDataDelivered = [];
        $amountsDelivered = [];

        for ($q = 1; $q <= 4; $q++) {
            $createdInQuarter = $cards->filter(
                fn (GiftCard $card) => $card->created_at?->year === $year
                    && $card->created_at->quarter === $q
            );

            $pendingInQuarter = $createdInQuarter->filter(
                fn (GiftCard $card) => $card->delivery_date === null
            );

            $deliveredInQuarter = $cards->filter(
                fn (GiftCard $card) => $card->delivery_date?->year === $year
                    && $card->delivery_date->quarter === $q
            );

            // Recibidas
            $quarterDataTotal[] = $createdInQuarter->count();
            $amountsTotal[] = '€ '.number_format((float) $createdInQuarter->sum('amount'), 2);

            // Por entregar
            $quarterDataPending[] = $pendingInQuarter->count();
            $amountsPending[] = '€ '.number_format((float) $pendingInQuarter->sum('amount'), 2);

            // Entregadas
            $quarterDataDelivered[] = $deliveredInQuarter->count();
            $amountsDelivered[] = '€ '.number_format((float) $deliveredInQuarter->sum('amount'), 2);
        }

        // Stat 1: Tarjetas por entregar
        $stats[] = Stat::make('Tarjetas por Entregar del Año '.$year, implode(' | ', $quarterDataPending))
            ->description(implode(' | ', $amountsPending))
            ->chart($quarterDataPending)
            ->descriptionIcon('heroicon-o-clock')
            ->color('warning');

        // Stat 2: Tarjetas entregadas
        $stats[] = Stat::make('Tarjetas Entregadas del Año '.$year, implode(' | ', $quarterDataDelivered))
            ->description(implode(' | ', $amountsDelivered))
            ->chart($quarterDataDelivered)
            ->descriptionIcon('heroicon-o-check-circle')
            ->color('success');

        // Stat 3: Todas las tarjetas
        $stats[] = Stat::make('Tarjetas Recibidas del Año '.$year, implode(' | ', $quarterDataTotal))
            ->description(implode(' | ', $amountsTotal))
            ->chart($quarterDataTotal)
            ->descriptionIcon('heroicon-o-calendar')
            ->color('info');

        return $stats;
    }

    protected function getColumns(): int
    {
        return 3; // Puedes ajustar según tu diseño
    }
}
