<?php

namespace App\Filament\Widgets;

use App\Models\Beneficiary;
use App\Models\Aid;
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

    //funciones para las estadisticas
    //funcion para el total de familias
    protected function totalbeneficiaries() {
        $beneficiaries = Beneficiary::all();
        $totalbeneficiaries = $beneficiaries->count();
        return $totalbeneficiaries;
    }
    //funcion para el total de ayudas en el año en curso
    protected function totalaids() {
        $currentYear = Carbon::now()->year;
        $aids = Aid::whereYear('start_date', $currentYear)->get();
        $totalaids = $aids->count();
        return $totalaids;
    }
    //funcion de total montos de ayuda en el año en curso
    protected function totalAidsAmount() {
        $currentYear = Carbon::now()->year;
        $aids = Aid::whereYear('created_at', $currentYear)->get();
        $totalAmount = $aids->sum('total_amount');
        return $totalAmount;
    }
}
