@php
    $logoPath = 'storage/' . setting('parish.vertical_logo');
    if (setting('parish.vertical_logo') && file_exists(public_path($logoPath))) {
        $logo = public_path($logoPath);
    } else {
        $logo = public_path('images/logo-v.svg');
    }

    // Lugar y fecha de emisión: "Sevilla, 17 de noviembre de 2026".
    $ciudad = trim((string) setting('parish.city'));
    $lugarYFecha = ($ciudad !== '' ? $ciudad . ', ' : '') . $record->issuedDateLong();
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
            font-size: 13px;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        .membrete {
            margin-top: 12px;
            text-align: left;
            font-size: 11px;
            line-height: 1.5;
        }

        .membrete .entidad {
            font-size: 13px;
            font-weight: bold;
        }

        .fecha {
            margin-top: 14px;
            font-size: 12px;
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

        .destinatario {
            margin-top: 30px;
            text-align: center;
        }

        .destinatario .nombre {
            font-size: 15px;
            font-weight: bold;
        }

        .destinatario .documento {
            font-size: 13px;
        }

        .cuerpo {
            margin-top: 25px;
            text-align: justify;
            line-height: 1.8;
            font-size: 14px;
        }

        .cuerpo p {
            margin: 0 0 10px;
        }

        .cuerpo ul,
        .cuerpo ol {
            margin: 0 0 10px;
            padding-left: 22px;
        }

        .cuerpo li {
            margin-bottom: 4px;
        }

        .cuerpo h1,
        .cuerpo h2,
        .cuerpo h3,
        .cuerpo h4 {
            margin: 0 0 10px;
            font-size: 15px;
        }

        .cuerpo blockquote {
            margin: 0 0 10px;
            padding-left: 10px;
            border-left: 2px solid #999999;
            color: #444444;
        }

        .cuerpo a {
            color: #1a4f9c;
            text-decoration: underline;
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
        <img src="{{ $logo }}" alt="Logo" class="logo" />

        <div class="membrete">
            <span class="entidad">{{ setting('parish.caritas_name') }}</span><br />
            {{ setting('parish.address') }}, {{ setting('parish.zip_code') }} {{ setting('parish.city') }}<br />
            Tel: {{ setting('parish.phone') }} | Email: {{ setting('parish.email') }}
        </div>

        <div class="fecha">{{ $lugarYFecha }}</div>
    </header>

    <main>
        <h1 class="titulo">{{ $record->title }}</h1>

        <div class="destinatario">
            <span class="nombre">D./Dña. {{ $record->recipient_name }}</span>
            @if (filled($record->recipient_document))
                <br />
                <span class="documento">DNI / NIE / PAS: {{ $record->recipient_document }}</span>
            @endif
        </div>

        <div class="cuerpo">{!! $record->renderedBody() !!}</div>

        <div class="firma">
            <div class="firma-linea"></div>
            <span class="firma-nombre">{{ setting('parish.caritas_director') }}</span>
            <span class="firma-cargo">Director de Cáritas Parroquial</span>
        </div>
    </main>
</body>

</html>
