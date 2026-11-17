<?php

use App\Filament\Resources\CertificateResource\Pages\CreateCertificate;
use App\Filament\Resources\CertificateResource\Pages\ListCertificates;
use App\Models\Beneficiary;
use App\Models\Certificate;
use App\Models\Family;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(new User);

    $volunteer = Volunteer::query()->create(['name' => 'Voluntario de prueba']);

    $this->beneficiario = Beneficiary::query()->create([
        'name' => 'Beneficiaria de prueba',
        'volunteer_id' => $volunteer->id,
        'state' => 'Activo',
    ]);

    $this->familiar = Family::query()->create([
        'name' => 'Familiar de prueba',
        'beneficiary_id' => $this->beneficiario->id,
    ]);
});

it('crea un certificado para un beneficiario con su nombre', function () {
    Livewire::test(CreateCertificate::class)
        ->fillForm([
            'recipient_type' => 'beneficiario',
            'beneficiary_id' => $this->beneficiario->id,
            'title' => 'Certificado de empadronamiento',
            'body' => 'Que la persona arriba indicada figura empadronada en esta parroquia.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $certificado = Certificate::query()->latest('id')->first();

    expect($certificado->recipient_name)->toBe('Beneficiaria de prueba')
        ->and($certificado->beneficiary_id)->toBe($this->beneficiario->id)
        ->and($certificado->family_id)->toBeNull();
});

it('crea un certificado para un familiar con su nombre', function () {
    Livewire::test(CreateCertificate::class)
        ->fillForm([
            'recipient_type' => 'familiar',
            'family_id' => $this->familiar->id,
            'title' => 'Autorización',
            'body' => 'Autorizo a la persona arriba indicada a recoger la ayuda.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $certificado = Certificate::query()->latest('id')->first();

    expect($certificado->recipient_name)->toBe('Familiar de prueba')
        ->and($certificado->family_id)->toBe($this->familiar->id)
        ->and($certificado->beneficiary_id)->toBeNull();
});

it('crea un certificado para alguien que no está en la base de datos', function () {
    Livewire::test(CreateCertificate::class)
        ->fillForm([
            'recipient_type' => 'externo',
            'recipient_name' => 'Persona externa',
            'title' => 'Solicitud',
            'body' => 'Solicito la colaboración de Cáritas para el caso que se describe.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $certificado = Certificate::query()->latest('id')->first();

    expect($certificado->recipient_name)->toBe('Persona externa')
        ->and($certificado->beneficiary_id)->toBeNull()
        ->and($certificado->family_id)->toBeNull();
});

it('cambia los campos del destinatario según el tipo elegido', function () {
    Livewire::test(CreateCertificate::class)
        ->assertFormFieldVisible('beneficiary_id')
        ->assertFormFieldHidden('family_id')
        ->assertFormFieldHidden('recipient_name')
        ->fillForm(['recipient_type' => 'familiar'])
        ->assertFormFieldHidden('beneficiary_id')
        ->assertFormFieldVisible('family_id')
        ->fillForm(['recipient_type' => 'externo'])
        ->assertFormFieldHidden('family_id')
        ->assertFormFieldVisible('recipient_name');
});

it('exige el nombre libre cuando el destinatario es externo', function () {
    Livewire::test(CreateCertificate::class)
        ->fillForm([
            'recipient_type' => 'externo',
            'title' => 'Certificado',
            'body' => 'Texto del certificado.',
        ])
        ->call('create')
        ->assertHasFormErrors(['recipient_name' => 'required']);
});

it('abre la previsualización del pdf desde el listado', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => 'Texto del certificado.',
    ]);

    Livewire::test(ListCertificates::class)
        ->callTableAction('previewPdf', $certificado)
        ->assertHasNoActionErrors();
});

it('imprime el membrete, el título, el texto y el director', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => "Primera línea.\nSegunda línea.",
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    expect($html)->toContain(setting('parish.caritas_name'))
        ->and($html)->toContain(setting('parish.address'))
        ->and($html)->toContain(setting('parish.caritas_director'))
        ->and($html)->toContain('Director de Cáritas Parroquial')
        ->and($html)->toContain('Certificado')
        ->and($html)->toContain('Primera línea.')
        ->and($html)->toContain('Segunda línea.');
});

it('pone el logo de la parroquia a la izquierda y los datos a la derecha', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => 'Texto del certificado.',
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    // El logo va en la primera celda de la cabecera y los datos en la segunda.
    expect(strpos($html, 'images/logo-v.svg'))->toBeLessThan(strpos($html, setting('parish.address')))
        ->and($html)->not->toContain('caritas-diosesana.png');
});
