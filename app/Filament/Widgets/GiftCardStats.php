<?php

namespace App\Filament\Widgets;

use App\Models\GiftCard;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GiftCardStats extends BaseWidget
{
    protected function getStats(): array
    {
        // Tarjetas sin entregar
        $tarjetasSinEntregar = GiftCard::whereNull('delivery_date')->count();
        $totalSinEntregar = GiftCard::whereNull('delivery_date')->sum('amount');

        // Tarjetas entregadas
        $tarjetasEntregadas = GiftCard::whereNotNull('delivery_date')->count();
        $totalEntregadas = GiftCard::whereNotNull('delivery_date')->sum('amount');

        // Total general de tarjetas
        $totalTarjetas = GiftCard::count();
        $montoTotal = GiftCard::sum('amount');

        return [
            Stat::make('Tarjetas sin entregar', $tarjetasSinEntregar)
                ->description('Total: € ' . number_format($totalSinEntregar, 2))
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Tarjetas entregadas', $tarjetasEntregadas)
                ->description('Total: € ' . number_format($totalEntregadas, 2))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total de tarjetas', $totalTarjetas)
                ->description('Monto total: € ' . number_format($montoTotal, 2))
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('gray'),
        ];
    }
}