<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\GiftCard;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GiftCardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

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
            $start = Carbon::createFromDate(now()->year, ($q - 1) * 3 + 1, 1)->startOfDay();
            $end = (clone $start)->addMonths(2)->endOfMonth()->endOfDay();

            // Recibidas
            $quarterDataTotal[] = GiftCard::whereBetween('created_at', [$start, $end])->count();
            $amountsTotal[] = '€ ' . number_format(GiftCard::whereBetween('created_at', [$start, $end])->sum('amount'), 2);

            // Por entregar
            $quarterDataPending[] = GiftCard::whereNull('delivery_date')->whereBetween('created_at', [$start, $end])->count();
            $amountsPending[] = '€ ' . number_format(GiftCard::whereNull('delivery_date')->whereBetween('created_at', [$start, $end])->sum('amount'), 2);

            // Entregadas
            $quarterDataDelivered[] = GiftCard::whereNotNull('delivery_date')->whereBetween('delivery_date', [$start, $end])->count();
            $amountsDelivered[] = '€ ' . number_format(GiftCard::whereNotNull('delivery_date')->whereBetween('delivery_date', [$start, $end])->sum('amount'), 2);
        }

        
        // Stat 1: Tarjetas por entregar
        $stats[] = Stat::make('Tarjetas por Entregar del Año ' . now()->year, implode(' | ', $quarterDataPending))
        ->description(implode(' | ', $amountsPending))
        ->chart($quarterDataPending)
        ->descriptionIcon('heroicon-o-clock')
        ->color('warning');
        
        // Stat 2: Tarjetas entregadas
        $stats[] = Stat::make('Tarjetas Entregadas del Año ' . now()->year, implode(' | ', $quarterDataDelivered))
        ->description(implode(' | ', $amountsDelivered))
        ->chart($quarterDataDelivered)
        ->descriptionIcon('heroicon-o-check-circle')
        ->color('success');
        
        // Stat 3: Todas las tarjetas
        $stats[] = Stat::make('Tarjetas Recibidas del Año ' . now()->year, implode(' | ', $quarterDataTotal))
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
