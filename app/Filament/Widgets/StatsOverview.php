<?php

namespace App\Filament\Widgets;

use App\Models\Aid;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Familias', $this->totalbeneficiaries()),
            Stat::make('Ayudas en el Año', $this->totalaids()),
            Stat::make('Total de Ayudas', '€ '.number_format($this->totalAidsAmount(), 2, ',', '.')),
        ];
    }

    // funciones para las estadisticas
    // funcion para el total de familias
    protected function totalbeneficiaries(): int
    {
        return Beneficiary::query()->count();
    }

    // funcion para el total de ayudas en el año en curso
    protected function totalaids(): int
    {
        return Aid::query()
            ->whereYear('start_date', Carbon::now()->year)
            ->count();
    }

    // funcion de total montos de ayuda en el año en curso
    protected function totalAidsAmount(): float
    {
        return (float) Aid::query()
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');
    }
}
