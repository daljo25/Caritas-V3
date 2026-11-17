<?php

use App\Filament\Resources\BeneficiaryResource\Pages\EditBeneficiary;
use App\Filament\Resources\BeneficiaryResource\Pages\ListBeneficiaries;
use App\Filament\Resources\BeneficiaryResource\RelationManagers\FamilyRelationManager;
use App\Filament\Resources\FamilyResource\Pages\ListFamilies;
use App\Filament\Resources\WorkExperienceResource\Pages\CreateWorkExperience;
use App\Filament\Resources\WorkExperienceResource\Pages\ListWorkExperiences;
use App\Models\Beneficiary;
use App\Models\Family;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\WorkExperience;
use Filament\Actions\CreateAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(new User);

    $this->volunteer = Volunteer::query()->create(['name' => 'Voluntario de prueba']);
    $this->pintura = WorkExperience::query()->create(['name' => 'Pintura '.uniqid()]);
    $this->albanil = WorkExperience::query()->create(['name' => 'Albañilería '.uniqid()]);

    $this->nuevoBeneficiario = fn (array $attributes = []) => Beneficiary::query()->create($attributes + [
        'name' => 'Persona '.uniqid(),
        'volunteer_id' => $this->volunteer->id,
        'state' => 'Activo',
    ]);
});

it('siembra el catálogo inicial de experiencias', function () {
    $sembradas = WorkExperience::query()->whereIn('name', [
        'Limpieza', 'Construcción y albañilería', 'Pintura', 'Electricidad', 'Conducción',
        'Cristalería', 'Carpintería',
    ])->count();

    expect($sembradas)->toBe(7)
        ->and(WorkExperience::query()->where('name', 'Cristalería y carpintería')->exists())->toBeFalse();
});

it('filtra los beneficiarios por experiencia laboral', function () {
    $conPintura = ($this->nuevoBeneficiario)();
    $conPintura->workExperiences()->attach($this->pintura);

    $sinExperiencia = ($this->nuevoBeneficiario)();

    Livewire::test(ListBeneficiaries::class)
        ->filterTable('workExperiences', [$this->pintura->id])
        ->assertCanSeeTableRecords([$conPintura])
        ->assertCanNotSeeTableRecords([$sinExperiencia]);
});

it('filtra los familiares por experiencia laboral', function () {
    $beneficiario = ($this->nuevoBeneficiario)();

    $conPintura = Family::query()->create([
        'name' => 'Familiar '.uniqid(),
        'beneficiary_id' => $beneficiario->id,
    ]);
    $conPintura->workExperiences()->attach($this->pintura);

    $sinExperiencia = Family::query()->create([
        'name' => 'Familiar '.uniqid(),
        'beneficiary_id' => $beneficiario->id,
    ]);

    Livewire::test(ListFamilies::class)
        ->filterTable('workExperiences', [$this->pintura->id])
        ->assertCanSeeTableRecords([$conPintura])
        ->assertCanNotSeeTableRecords([$sinExperiencia]);
});

it('aplica el filtro que enlaza la experiencia desde su listado', function () {
    $conPintura = ($this->nuevoBeneficiario)();
    $conPintura->workExperiences()->attach($this->pintura);

    $sinExperiencia = ($this->nuevoBeneficiario)();

    // Formato que genera el botón "Ver beneficiarios" del catálogo.
    Livewire::withQueryParams([
        'filters' => ['workExperiences' => ['values' => [$this->pintura->id]]],
    ])
        ->test(ListBeneficiaries::class)
        ->assertCanSeeTableRecords([$conPintura])
        ->assertCanNotSeeTableRecords([$sinExperiencia]);
});

it('guarda las experiencias elegidas en la ficha', function () {
    $beneficiario = ($this->nuevoBeneficiario)();

    Livewire::test(EditBeneficiary::class, ['record' => $beneficiario->getKey()])
        ->fillForm([
            'workExperiences' => [$this->pintura->id, $this->albanil->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($beneficiario->fresh()->workExperiences()->pluck('work_experiences.id')->sort()->values()->all())
        ->toBe(collect([$this->pintura->id, $this->albanil->id])->sort()->values()->all());
});

it('guarda la experiencia laboral al crear un familiar desde la ficha del beneficiario', function () {
    $beneficiario = ($this->nuevoBeneficiario)();

    Livewire::test(FamilyRelationManager::class, [
        'ownerRecord' => $beneficiario,
        'pageClass' => EditBeneficiary::class,
    ])
        ->callTableAction(CreateAction::class, data: [
            'name' => 'Familiar con oficio',
            'workExperiences' => [$this->pintura->id],
        ])
        ->assertHasNoTableActionErrors();

    $familiar = Family::query()->where('name', 'Familiar con oficio')->firstOrFail();

    expect($familiar->workExperiences()->pluck('work_experiences.id')->all())
        ->toBe([$this->pintura->id]);
});

it('crea una experiencia nueva desde el catálogo', function () {
    $nombre = 'Soldadura '.uniqid();

    Livewire::test(CreateWorkExperience::class)
        ->fillForm(['name' => $nombre])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(WorkExperience::query()->where('name', $nombre)->exists())->toBeTrue();
});

it('muestra en el catálogo cuántas personas tienen cada experiencia', function () {
    $beneficiario = ($this->nuevoBeneficiario)();
    $beneficiario->workExperiences()->attach($this->pintura);

    Livewire::test(ListWorkExperiences::class)
        ->searchTable($this->pintura->name)
        ->assertCanSeeTableRecords([$this->pintura])
        ->assertTableColumnStateSet('beneficiaries_count', 1, $this->pintura)
        ->assertTableColumnStateSet('families_count', 0, $this->pintura);
});
