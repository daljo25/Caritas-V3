<?php

namespace App\Exports;

use App\Models\MonthlyReport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyReportExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // Función query con el filtro year
    public function query()
    {
        $query = MonthlyReport::query()
            ->select(
                'month',
                'year',
                'collection',
                'parroquial_receipt',
                'bank_donation',
                'volunteer_campaign_donation',
                'diosesano_receipt',
                'diosesano_donation',
                'other_donation',
                'special_donation',
                'transfer_collection',
                'transfer_membership',
                'transfer_campaign',
                'transfer_other',
                'transfer_arciprestal',
                'health',
                'housing',
                'food',
                'supplies_receipt',
                'other_intervention',
                'parish_project',
                'general_expense',
                'other_entity',
                'campaign_volunteers',
                'campaign_local_emergency',
                'campaign_international_emergency',
                'development_cooperation',
                'message'
            );

        if (!empty($this->filters['year'])) {
            $query->whereIn('year', $this->filters['year']);
        }

        return $query;
    }

    // Cabeceras de la tabla
    public function headings(): array
    {
        return [
            'Mes',
            'Año',
            'Colecta',
            'Recibo Parroquial',
            'Donativo por Banco',
            'Donativo Campaña de Voluntarios',
            'Recibo Diosesano',
            'Donativo Diosesano',
            'Otro Donativo',
            'Donativo Especial',
            'Transferencia de Colecta',
            'Transferencia de Socios',
            'Transferencia de Campaña',
            'Otra Transferencia',
            'Transferencia Arciprestal',
            'Salud',
            'Vivienda',
            'Alimentación',
            'Recibo de Suministros',
            'Otra Intervención',
            'Proyecto Parroquial',
            'Gasto General',
            'Otra Entidad',
            'Campaña de Voluntarios',
            'Emergencia Local',
            'Emergencia Internacional',
            'Cooperación al Desarrollo',
            'Mensaje',
        ];
    }

    // Transformar cada fila antes de exportarla
    public function map($row): array
    {
        return [
            $row->month,
            $row->year,
            $row->collection,
            $row->parroquial_receipt,
            $row->bank_donation,
            $row->volunteer_campaign_donation,
            $row->diosesano_receipt,
            $row->diosesano_donation,
            $row->other_donation,
            $row->special_donation,
            $row->transfer_collection,
            $row->transfer_membership,
            $row->transfer_campaign,
            $row->transfer_other,
            $row->transfer_arciprestal,
            $row->health,
            $row->housing,
            $row->food,
            $row->supplies_receipt,
            $row->other_intervention,
            $row->parish_project,
            $row->general_expense,
            $row->other_entity,
            $row->campaign_volunteers,
            $row->campaign_local_emergency,
            $row->campaign_international_emergency,
            $row->development_cooperation,
            strip_tags($row->message), // Eliminar etiquetas HTML del mensaje
        ];
    }
}
