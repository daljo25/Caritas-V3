<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use App\Models\Donor;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonorStatsOverview extends BaseWidget
{
    protected static ?int $sort = 4;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Donadores', $this->totaldonors()),
            Stat::make('Donaciones en el Año', '€ '.number_format($this->totalDonationsThisYear(), 2, ',', '.')),
            Stat::make('Total de Donaciones', '€ '.number_format($this->totalDonations(), 2, ',', '.')),
        ];
    }

    // funciones para las estadisticas
    // funcion para el total de donadores
    protected function totaldonors(): int
    {
        return Donor::query()->count();
    }

    // funcion de total montos de Donaciones
    protected function totalDonations(): float
    {
        return (float) Donation::query()->sum('amount');
    }

    // funcion de total de donaciones este año
    protected function totalDonationsThisYear(): float
    {
        return (float) Donation::query()
            ->where('donation_year', Carbon::now()->year)
            ->sum('amount');
    }
}
