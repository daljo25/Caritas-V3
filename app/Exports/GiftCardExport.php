<?php

namespace App\Exports;

use App\Models\GiftCard;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class GiftCardExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Obtiene la colección de registros de GiftCard según los filtros proporcionados.
     */
    public function collection(): Collection
    {
        $query = GiftCard::query()->with('aid.beneficiary'); // Cargar la relación `aid` y `beneficiary`

        // Filtrar por estado (entregada o no entregada)
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'entregada') {
                $query->whereNotNull('delivery_date'); // Tarjetas entregadas
            } elseif ($this->filters['status'] === 'no_entregada') {
                $query->whereNull('delivery_date'); // Tarjetas no entregadas
            }
        }

        // Filtrar por rango de fechas (fecha de entrega)
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('delivery_date', [
                $this->filters['start_date'],
                $this->filters['end_date'],
            ]);
        } elseif (!empty($this->filters['start_date'])) {
            $query->where('delivery_date', '>=', $this->filters['start_date']);
        } elseif (!empty($this->filters['end_date'])) {
            $query->where('delivery_date', '<=', $this->filters['end_date']);
        }

        return $query->get();
    }

    /**
     * Devuelve la información para cada fila de la exportación.
     */
    public function map($giftcard): array
    {
        return [
            $giftcard->serie,
            $giftcard->number,
            $giftcard->pin,
            $giftcard->amount,
            $giftcard->aid_id ? $giftcard->aid_id : 'N/A', // ID de la ayuda
            $giftcard->aid_id ? $giftcard->aid->beneficiary->name : 'N/A', // Nombre del beneficiario
            $giftcard->issuer,
            $giftcard->exp,
            $giftcard->delivery_date ? Carbon::parse($giftcard->delivery_date)->format('d-m-Y') : 'No entregada', // Fecha de entrega
        ];
    }

    /**
     * Define los encabezados de las columnas para el archivo Excel.
     */
    public function headings(): array
    {
        return [
            'Serie',
            'Número',
            'PIN',
            'Monto',
            'ID de Ayuda',
            'Beneficiario',
            'Emisor',
            'Expiración',
            'Fecha de Entrega',
        ];
    }

    /**
     * Título personalizado para la hoja de Excel.
     */
    public function title(): string
    {
        return 'Tarjetas de Regalo';
    }
}