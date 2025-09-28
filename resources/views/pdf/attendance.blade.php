<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificado de Asistencia</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14pt;
            line-height: 1.5;
            margin: 2cm;
        }
        .logo {
            width: 120px;
            margin-bottom: 15px;
        }
        .header {
            text-align: left;
            margin-bottom: 30px;
        }
        .certificate-number {
            text-align: right;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .institution-info {
            text-align: left;
            margin-bottom: 15px;
            font-size: 12pt;
        }
        .content {
            margin: 0 0 40px 0;
            text-align: justify;
        }
        .signature {
            margin-top: 80px;
            text-align: center;
        }
        .signature-line {
            width: 300px;
            border-top: 1px solid black;
            margin: 0 auto;
            padding-top: 5px;
        }
        .underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/' . setting('parish.vertical_logo')) ?? asset('images/logo-v.svg') }}" alt="Logo" class="logo">
        <div class="certificate-number">N° {{ $attendance->certificate_number }}</div>
    </div>

    <div class="institution-info">
        <strong>{{ setting('parish.caritas_name') }}</strong><br>
        {{ setting('parish.address') }}, {{ setting('parish.zip_code') }} {{ setting('parish.city') }}<br>
        Tel: {{ setting('parish.phone') }} | Email: {{ setting('parish.email') }}
    </div>

    <div class="content">
        <p>Por medio del presente, <strong>certificamos</strong> que:</p>

        <p style="text-align: center; margin: 15px 0;">
            <strong>Sr./Sra. {{ $attendance->beneficiary->name }}</strong><br>
            Documento de Identidad: {{ $attendance->beneficiary->dni }}
        </p>

        <p>
            Asistió puntualmente a su cita programada el día 
            <span class="underline">{{ date('d/m/Y', strtotime($attendance->attendance_date)) }}</span> 
            a las 
            <span class="underline">{{ date('h:i A', strtotime($attendance->attendance_time)) }}</span>, 
            en nuestras instalaciones ubicadas en {{ setting('parish.address') }},{{ setting('parish.city') }}, para recibir 
            <span class="underline">{{ $attendance->purpose }}</span>.
        </p>

        <p>
            Este documento se expide a solicitud del interesado en {{ setting('parish.city') }} el {{ date('d/m/Y') }} para los fines que estime conveniente.
        </p>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p>
            <strong>{{ setting('parish.caritas_director') }}</strong><br>
            Director de Cáritas Parroquial
        </p>
    </div>

</body>
</html>