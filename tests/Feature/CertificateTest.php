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

    $this->beneficiarioDni = 'DOC-BEN-'.uniqid();
    $this->familiarDni = 'DOC-FAM-'.uniqid();

    $this->beneficiario = Beneficiary::query()->create([
        'name' => 'Beneficiaria de prueba',
        'dni' => $this->beneficiarioDni,
        'volunteer_id' => $volunteer->id,
        'state' => 'Activo',
    ]);

    $this->familiar = Family::query()->create([
        'name' => 'Familiar de prueba',
        'dni' => $this->familiarDni,
        'beneficiary_id' => $this->beneficiario->id,
    ]);
});

it('crea un certificado para un beneficiario con su nombre y su documento', function () {
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
        ->and($certificado->recipient_document)->toBe($this->beneficiarioDni)
        ->and($certificado->beneficiary_id)->toBe($this->beneficiario->id)
        ->and($certificado->family_id)->toBeNull();
});

it('crea un certificado para un familiar con su nombre y su documento', function () {
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
        ->and($certificado->recipient_document)->toBe($this->familiarDni)
        ->and($certificado->family_id)->toBe($this->familiar->id)
        ->and($certificado->beneficiary_id)->toBeNull();
});

it('crea un certificado para alguien que no está en la base de datos', function () {
    Livewire::test(CreateCertificate::class)
        ->fillForm([
            'recipient_type' => 'externo',
            'recipient_name' => 'Persona externa',
            'recipient_document' => 'X1234567L',
            'title' => 'Solicitud',
            'body' => 'Solicito la colaboración de Cáritas para el caso que se describe.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $certificado = Certificate::query()->latest('id')->first();

    expect($certificado->recipient_name)->toBe('Persona externa')
        ->and($certificado->recipient_document)->toBe('X1234567L')
        ->and($certificado->beneficiary_id)->toBeNull()
        ->and($certificado->family_id)->toBeNull();
});

it('guarda el html del editor enriquecido en el documento', function () {
    Livewire::test(CreateCertificate::class)
        ->fillForm([
            'recipient_type' => 'externo',
            'recipient_name' => 'Persona externa',
            'title' => 'Certificado',
            'body' => '<p>Texto con <strong>estilos</strong>.</p>',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Certificate::query()->latest('id')->first()->body)
        ->toBe('<p>Texto con <strong>estilos</strong>.</p>');
});

it('cambia los campos del destinatario según el tipo elegido', function () {
    Livewire::test(CreateCertificate::class)
        ->assertFormFieldVisible('beneficiary_id')
        ->assertFormFieldHidden('family_id')
        ->assertFormFieldHidden('recipient_name')
        ->assertFormFieldHidden('recipient_document')
        ->fillForm(['recipient_type' => 'familiar'])
        ->assertFormFieldHidden('beneficiary_id')
        ->assertFormFieldVisible('family_id')
        ->fillForm(['recipient_type' => 'externo'])
        ->assertFormFieldHidden('family_id')
        ->assertFormFieldVisible('recipient_name')
        ->assertFormFieldVisible('recipient_document');
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

it('imprime el membrete, el título, el destinatario, el texto y el director', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'recipient_document' => 'X1234567L',
        'title' => 'Certificado',
        'body' => '<p>Primera línea con <strong>negrita</strong>.</p><p>Segunda línea.</p>',
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    expect($html)->toContain(setting('parish.caritas_name'))
        ->and($html)->toContain(setting('parish.address'))
        ->and($html)->toContain(setting('parish.caritas_director'))
        ->and($html)->toContain('Director de Cáritas Parroquial')
        ->and($html)->toContain('Certificado')
        ->and($html)->toContain('Persona externa')
        ->and($html)->toContain('X1234567L')
        // El HTML del editor se imprime como tal, no escapado.
        ->and($html)->toContain('<p>Primera línea con <strong>negrita</strong>.</p>')
        ->and($html)->toContain('Segunda línea.')
        ->and($html)->not->toContain('&lt;p&gt;');
});

it('sanea el html del editor antes de imprimirlo', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => '<p>Texto <strong>válido</strong>.</p><script>alert("xss")</script>',
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    expect($html)->toContain('<strong>válido</strong>')
        ->and($html)->not->toContain('<script>');
});

it('respeta los saltos de línea de un texto plano', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => "Primera línea.\nSegunda línea.",
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    expect($html)->toContain("Primera línea.<br />\nSegunda línea.");
});

it('no imprime la línea del documento cuando el destinatario no tiene', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => 'Texto del certificado.',
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    expect($html)->toContain('Persona externa')
        ->and($html)->not->toContain('DNI / NIE / PAS:');
});

it('pone el logo de la parroquia encima de los datos, alineados a la izquierda', function () {
    $certificado = Certificate::query()->create([
        'recipient_type' => 'externo',
        'recipient_name' => 'Persona externa',
        'title' => 'Certificado',
        'body' => 'Texto del certificado.',
    ]);

    $html = view('pdf.certificate', ['record' => $certificado])->render();

    // El logo va primero y debajo el membrete de la parroquia.
    expect(strpos($html, 'images/logo-v.svg'))->toBeLessThan(strpos($html, setting('parish.address')))
        ->and($html)->not->toContain('caritas-diosesana.png');
});
