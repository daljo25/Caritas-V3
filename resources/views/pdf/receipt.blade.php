<!DOCTYPE html>
<html lang="es">

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <!-- Latest compiled and minified CSS -->

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

        .logo {
            width: 200px;
            height: auto;
        }

        .icon {
            width: 15px;
            height: auto;
            vertical-align: middle;
        }

        .row {
            width: 100%;
            margin: 0;
            display: block;
            float: left;
        }

        .Recibo {
            text-align: right;

        }

        table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #333;
            margin: 0;
            border-collapse: collapse;

        }

        .noborder {
            border: none;
        }

        .Firma {
            vertical-align: top;
            height: 80px;
        }

        .text-justify {
            text-align: justify;
        }
    </style>


</head>

<body>
    <header>
        <table>
            <tr>
                <td colspan="2" width="50%" class="noborder"><img
                        src="{{ public_path('storage/' . setting('caritas.logo')) ?? asset('images/caritas-diosesana.png') }}" alt="Logo" class="logo" /></td>
            </tr>
            <tr>
                <td width="50%" class="noborder">
                    <h2>RECEPCIÓN DE AYUDA</h2>
                </td>
                <td width="50%" class="Recibo noborder">Recibo de Ayuda Nº. {{ $record->id }} </td>
            </tr>
        </table>

    </header>

    <main>

        <table>
            <tr>
                <td width="10%">D./Dña.</td>
                <td width="90%"><strong>{{ $record->Beneficiary->id }} {{ $record->Beneficiary->name }}</strong></td>

            </tr>
        </table>
        <table>
            <tr>
                <td width="20%">Con DNI/ NIE o nº de pasaporte</td>
                <td width="80%"><strong>{{ $record->Beneficiary->dni }}</strong></td>

            </tr>
        </table>
        <p class="text-justify">
            <strong>Ha recibido la cantidad de
                {{ $record->approved_amount }} euros en concepto de
                AYUDA para cubrir la/s necesidad/es especificadas a
                continuación:</strong>
        </p>
        <article class="text-justify">
            <strong><mark>LUCHA CONTRA LA POBREZA ENERGÉTICA</mark></strong>
        </article>
        <table>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Pago de suministro' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Pago de suministro</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Adquisición y reposición de elementos luminosos de bajo consumo' ? 'square-x' : 'square' . '.webp')) }}"
                        class="icon" />Adquisición y reposición de elementos luminosos de bajo consumo</td>
            </tr>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Mejora de aislamiento' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Mejora de aislamiento</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipamientos' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Adecuación, mejora, reparación y/o mantenimiento de instalaciones y
                    equipamientos
                </td>
            </tr>
            <tr>
                <td colspan="2" width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Otras necesidades básicas de energía' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Otras necesidades básicas de energía</td>
            </tr>
        </table>
        <article class="text-justify">
            <br />
            <strong><mark>GASTOS RELATIVOS A LA VIVIENDA</mark></strong>
        </article>
        <table>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Impago de Alquiler' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Impago de Alquiler</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Impago de crédito hipotecario' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Impago de crédito hipotecario</td>
            </tr>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Gastos derivados de las alternativas al alquiler' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Gastos derivados de las alternativas al alquiler</td>
                <td width="50%"><img
                        src="{{ public_path(
                            'images/' .
                                ($record->type ===
                                'Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipos NO
                                           relacionados con la eficiencia Energética'
                                    ? 'square-x'
                                    : 'square') .
                                '.webp',
                        ) }}"
                        class="icon" />Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipos NO
                    relacionados con la eficiencia Energética</td>
            </tr>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Equipamiento básico del Hogar' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Equipamiento básico del Hogar</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Ropería (Ropa, Zapatos, Uniformes, Lencería del Hogar, etc.)' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Ropería (Ropa, Zapatos, Uniformes, Lencería del Hogar, etc.)</td>
            </tr>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Reparación de Vehiculo' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Reparación de Vehiculo</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Otras necesidades básicas de vivienda' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Otras necesidades básicas de vivienda</td>
            </tr>
        </table>
        <article class="text-justify">
            <br />
            <strong><mark>GASTOS RELATIVOS A LA REDUCCIÓN DE LA BRECHA DIGITAL</mark></strong>
        </article>
        <table>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Pago de Telefonía e Internet' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Pago de Telefonía e Internet</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Equipamiento Digital' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Equipamiento Digital</td>
            </tr>
            <tr>
                <td colspan="2" width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Otras necesidades básicas de la brecha digital' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Otras necesidades básicas de la brecha digital</td>
            </tr>
        </table>
        <article class="text-justify">
            <br />
            <strong><mark>GASTOS RELATIVOS A LA EDUCACIÓN Y FORMACIÓN</mark></strong>
        </article>
        <table>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Material Escolar' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Material Escolar</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Servicios escolares (Aula Matinal, Aula de Medio dia, Comedor, Extraescolares, etc.)' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Servicios escolares (Aula Matinal, Aula de Medio dia, Comedor, Extraescolares,
                    etc.)
                </td>
            </tr>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Gastos de Transporte' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Gastos de Transporte</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Otras necesidades básicas de educación' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Otras necesidades básicas de educación</td>
            </tr>
        </table>
        <article class="text-justify">
            <br />
            <strong><mark>GASTOS RELATIVOS A LA SALUD</mark></strong>
        </article>
        <table>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Material farmacéutico (fármacos, copagos, etc.)' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Material farmacéutico (fármacos, copagos, etc.)</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Óptica y ortopedia' ? 'square-x' : 'square') . '.webp') }}" class="icon" />Óptica
                    y ortopedia</td>
            </tr>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Odontología' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Odontología</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Servicios terapéuticos' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Servicios terapéuticos</td>
            </tr>
            <tr>
                <td colspan="2" width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Otras necesidades básicas de salud' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Otras necesidades básicas de salud</td>
            </tr>
        </table>
        <article class="text-justify">
            <br />
            <strong><mark>OTRAS NECESIDADES BÁSICAS</mark></strong>
        </article>
        <table>
            <tr>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Alimentación e higiene' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Alimentación e higiene</td>
                <td width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Gastos de Transporte o Viajes' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Gastos de Transporte o Viajes</td>
            </tr>
            <tr>
                <td colspan="2" width="50%"><img
                        src="{{ public_path('images/' . ($record->type === 'Otras necesidades básicas' ? 'square-x' : 'square') . '.webp') }}"
                        class="icon" />Otras necesidades básicas</td>
            </tr>
        </table>

        </div>
    </main>

    <footer>
        <br />
        <table>
            <tr>
                <td width="60%" class="noborder"><img src="{{ public_path('images/' . 'square-x' . '.webp') }}"
                        class="icon" /> Autorizo el Pago a terceros <br />
                    En {{ setting('parish.city') }}, a {{ date('d/m/Y') }}</td>
                <td width="40%" class="Firma">Firma</td>
            </tr>
        </table>

    </footer>

</body>

</html>
