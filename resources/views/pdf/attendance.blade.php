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
        <img src="https://caritas.sagradocorazonbellavista.com/images/logo-v.svg" alt="Logo" class="logo">
        <div class="certificate-number">N° {{ $attendance->certificate_number }}</div>
    </div>

    <div class="institution-info">
        <strong>CARITAS PARROQUIAL SAGRADO CORAZÓN DE JESÚS</strong><br>
        C/ Asensio y Toledo, 25, Bellavista, Sevilla<br>
        Tel: 685524818 | Email: caritasbellavistacorazonjesus@gmail.com
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
            en nuestras instalaciones ubicadas en Bellavista, Sevilla, para recibir 
            <span class="underline">{{ $attendance->purpose }}</span>.
        </p>

        <p>
            Este documento se expide a solicitud del interesado en Sevilla el {{ date('d/m/Y') }} para los fines que estime conveniente.
        </p>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p>
            <strong>Sabino Antolí García</strong><br>
            Director de Cáritas
        </p>
    </div>

</body>
</html>