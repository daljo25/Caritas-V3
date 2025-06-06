<style>
    div {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 20px;
    }

    .logo {
        width: 150px;
    }

    .fecha {
        text-align: right;
    }

    .titulo {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        text-decoration: underline;
    }

    .firma {
        text-align: center;
        font-style: italic;
        font-weight: bold;

    }
</style>

<div><img src="{{ public_path('storage/' . setting('parish.vertical_logo')) }}" alt="Logo" class="logo"></div><br>
<div class="fecha">Fecha: {{setting('parish.city')}} {{ date('d/m/Y') }}</div>
<div>
    <p class="titulo">Carta de Derivación </p>
</div><br><br>
<div><strong>Dirigida a:</strong> {{ $record->Collaborator->name }}</div>
<div>Dirección: {{ $record->Collaborator->address }}</div>
<div>Teléfono: {{ $record->Collaborator->phone }}</div>
<div>Email: {{ $record->Collaborator->email }}</div><br><br>
<div><strong>Derivado por:</strong> {{setting('parish.caritas_name')}} {{setting('parish.city')}}.</div><br><br>
<div><strong>Nombre: </strong> {{ $record->Beneficiary->name }}</div>
<div>Dirección: {{ $record->Beneficiary->address }}</div>
<div>DNI / NIE / PAS: {{ $record->Beneficiary->dni }}</div><br><br>
<div>Motivo: {{ $record->reason }}</div><br>
<div>Observaciones: {{ $record->observation }}</div><br><br><br><br><br><br>
<div class="firma">Fdo. {{setting('parish.caritas_director')}}</div>
<div class="firma">Director de Caritas</div>
