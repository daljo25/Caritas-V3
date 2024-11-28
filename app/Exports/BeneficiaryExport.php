<?php

namespace App\Exports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiaryExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    /**
     * Obtiene todos los beneficiarios con su relación "Family".
     */
    public function collection()
    {
        return Beneficiary::with('Family')->get(); // Cargar la relación Family
    }

    /**
     * Devuelve la información para cada fila de la exportación.
     */
    public function map($beneficiary): array
    {
        // Calcular la cantidad de familiares por rango de edad
        $ageGroups = $this->getFamilyMemberAgeGroups($beneficiary);

        return [
            $beneficiary->state,
            $beneficiary->id,
            $beneficiary->name,
            $beneficiary->dni,
            $ageGroups['-2'],
            $ageGroups['2a8'],
            $ageGroups['8a14'],
            $ageGroups['14a18'],
            $ageGroups['+18'],
            $ageGroups['total'], // Total de familiares
            $beneficiary->address,
            $beneficiary->phone,
        ];
    }

    /**
     * Devuelve los encabezados para el archivo de Excel.
     */
    public function headings(): array
    {
        return [
            'Estado',
            'Nº',
            'Nombre',
            'DNI',
            '-2 años',
            '2 a 8 años',
            '8 a 14 años',
            '14 a 18 años',
            'Adultos',
            'Total',
            'Dirección',
            'Teléfono',
        ];
    }

    /**
     * Título personalizado para la hoja de Excel.
     */
    public function title(): string
    {
        return 'Listado de Usuarios';
    }
    /**
     * Descargar el archivo la hoja de Excel.
     */
    public function download()
    {
        return Excel::download(new BeneficiaryExport());
    }

    /**
     * Obtiene la cantidad de familiares por rango de edad.
     */
    private function getFamilyMemberAgeGroups($beneficiary)
    {
        $groups = [
            '-2' => 0,
            '2a8' => 0,
            '8a14' => 0,
            '14a18' => 0,
            '+18' => 0,
            'total' => 0,
        ];

        $today = Carbon::now();

        $processMember = function ($birthDate) use ($today, &$groups) {
            if (!$birthDate || !Carbon::hasFormat($birthDate, 'Y-m-d')) {
                return; // Ignorar fechas inválidas
            }

            $birthDate = Carbon::parse($birthDate);
            $age = $birthDate->diff($today)->y; // Años completos

            if ($age < 2) {
                $groups['-2']++;
            } elseif ($age <= 8) {
                $groups['2a8']++;
            } elseif ($age <= 14) {
                $groups['8a14']++;
            } elseif ($age <= 18) {
                $groups['14a18']++;
            } else {
                $groups['+18']++;
            }

            $groups['total']++;
        };

        // Procesar el beneficiario principal
        if ($beneficiary && $beneficiary->birth_date) {
            $processMember($beneficiary->birth_date);
        }

        // Procesar familiares
        if ($beneficiary && $beneficiary->Family) {
            foreach ($beneficiary->Family as $familyMember) {
                $processMember($familyMember->birth_date);
            }
        }

        return $groups;
    }
}
