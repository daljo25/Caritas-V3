@php
    $logoPath = 'storage/' . setting('parish.vertical_logo');
    if (setting('parish.vertical_logo') && file_exists(public_path($logoPath))) {
        $logo = public_path($logoPath);
    } else {
        $logo = public_path('images/logo-v.svg');
    }
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <style>
        body {
            padding: 0;
            margin: 30px;
            font-family: helvetica, sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .noborder {
            border: none;
        }

        .datos {
            width: 65%;
            font-size: 11px;
            line-height: 1.5;
            padding-top: 4px;
        }

        .datos .entidad {
            font-size: 13px;
            font-weight: bold;
        }

        .logo-cell {
            width: 35%;
        }

        .logo {
            width: 130px;
            height: auto;
        }

        .titulo {
            margin-top: 45px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
        }

        .cuerpo {
            margin-top: 35px;
            text-align: justify;
            line-height: 1.8;
            font-size: 14px;
        }

        .firma {
            margin-top: 70px;
            text-align: center;
        }

        .firma-linea {
            width: 260px;
            margin: 0 auto 8px;
            border-top: 1px solid #333;
        }

        .firma-nombre {
            font-weight: bold;
        }

        .firma-cargo {
            display: block;
            margin-top: 2px;
            font-size: 12px;
        }
    </style>

</head>

<body>
    <header>
        <table>
            <tr>
                <td class="noborder logo-cell"><img src="{{ $logo }}" alt="Logo" class="logo" /></td>
                <td class="noborder datos">
                    <span class="entidad">{{ setting('parish.caritas_name') }}</span><br />
                    {{ setting('parish.address') }}<br />
                    {{ setting('parish.zip_code') }} {{ setting('parish.city') }}<br />
                    Tel. {{ setting('parish.phone') }}<br />
                    {{ setting('parish.email') }}
                </td>
            </tr>
        </table>
    </header>

    <main>
        <h1 class="titulo">{{ $record->title }}</h1>

        <div class="cuerpo">{!! nl2br(e($record->body)) !!}</div>

        <div class="firma">
            <div class="firma-linea"></div>
            <span class="firma-nombre">{{ setting('parish.caritas_director') }}</span>
            <span class="firma-cargo">Director de Cáritas Parroquial</span>
        </div>
    </main>
</body>

</html>
