<?php

namespace App\Filament\Widgets;

use App\Models\Aid;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class StatsChart extends ChartWidget
{
    protected ?string $heading = 'Ayudas';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentYear = Carbon::now()->year;

        // Una sola consulta para todo el año: antes se hacía un count() por
        // cada mes (12 consultas por render del panel).
        $countsByMonth = Aid::query()
            ->whereYear('start_date', $currentYear)
            ->pluck('start_date')
            ->countBy(fn ($date) => Carbon::parse($date)->month);

        $totalAidsByMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $totalAidsByMonth[] = $countsByMonth[$month] ?? 0;
        }

        // Definimos el array de datos para el gráfico
        return [
            'datasets' => [
                [
                    'label' => 'Ayudas en el Año',
                    'data' => $totalAidsByMonth,
                ],
            ],
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
