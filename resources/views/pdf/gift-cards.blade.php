@php
    use app\Models\Aid;
    $cont = 0;
    $total = 0;

    $logoPath = 'storage/' . setting('parish.vertical_logo');
    if (setting('parish.vertical_logo') && file_exists(public_path($logoPath))) {
        $logo = public_path($logoPath);
    } else {
        $logo = public_path('images/logo-v.svg');
    }
    
    // Usar los parámetros mes y año si están disponibles, si no usar el mes actual
    $month = $month ?? \Carbon\Carbon::now()->format('m');
    $year = $year ?? \Carbon\Carbon::now()->format('Y');
    
    // Crear fechas de inicio y fin del mes
    $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
    $endDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <style>
        body {
            padding: 0;
            margin: 0;
            font-family: helvetica, sans-serif;
            font-size: 14px;
        }

        h2 {
            font-size: 18px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }
        thead {
            font-weight: bold;
            border: 1px solid #333;
            margin: 0;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #333;
            margin: 0;
            border-collapse: collapse;

        }
        .logo {
            width: 150px;
            height: auto;
        }

        .noborder {
            border: none;
        }

        .text-justify {
            text-align: justify;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>


</head>

<body>

    <table>
        <tr>
            <td class="noborder"><img src="{{ $logo }}" alt="logo" class="logo"></td>
            <td class="text-right noborder">{{setting('parish.caritas_name')}}<br>
                {{setting('parish.address')}} <br>
                {{setting('parish.zip_code')}} {{setting('parish.city')}}</td>
        </tr>
        <tr>
            <td class="noborder"></td>
            <td class="text-right noborder">{{setting('parish.city')}}, a {{ date("d/m/Y") }}</td>
        </tr>
    </table>
<br><br>
    <p class="text-center bold">
        Relación de Ayudas del Mes {{ $month }}/{{ $year }}.
    </p>
<br><br>
<table>
    <tr class="text-center bold">
        <td>Nº</td>
        <td>TITULAR</td>
        <td>DNI/NIE/PAS</td>
        <td>TARJETAS</td>
        <td>CANTIDAD</td>
    </tr>
    @foreach (Aid::where('type', 'Alimentación e higiene')
             ->where('paid_by', 'Diocesana')
             ->where(function ($query) use ($startDate, $endDate) {
                 // Mostrar ayudas donde el mes consultado está entre start_date y end_date
                 $query->whereDate('start_date', '<=', $endDate)
                       ->whereDate('end_date', '>=', $startDate);
             })
             ->with(['Beneficiary', 'giftCards' => function ($query) use ($startDate, $endDate) {
                 // Cargar solo las tarjetas entregadas en ese mes específico
                 $query->whereBetween('delivery_date', [$startDate, $endDate]);
             }])
             ->orderBy('id')
             ->get() as $aid)
             
        <tr class="text-center">
            <td>{{$aid->Beneficiary->id}}</td>
            <td>{{$aid->Beneficiary->name}}</td>
            <td>{{$aid->Beneficiary->dni}}</td>
            <td>
                @foreach ($aid->giftCards as $giftCard)
                    {{$giftCard->serie}}<br>
                @endforeach
            </td>
            <td>{{$aid->approved_amount}}</td>
        </tr>
        @php
            $cont++;
            $total += $aid->approved_amount;
        @endphp
    @endforeach
    <tr class="text-center">
        <td>{{$cont}} {{ $cont > 1 ? 'Familias' : 'Familia'}}</td>
        <td></td>
        <td></td>
        <td>Total</td>
        <td>{{number_format($total, 2, '.', ',')}}</td>
    </tr>
</table>
<br><br><br><br>
<div class="text-center">
    Fdo. {{setting('parish.caritas_director')}} <br>
    Director de Caritas Parroquial
</div>
</body>