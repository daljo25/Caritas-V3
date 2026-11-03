<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\BeneficiaryResource\RelationManagers\FamilyRelationManager;
use App\Filament\Resources\BeneficiaryResource\RelationManagers\RecordRelationManager;
use App\Filament\Resources\BeneficiaryResource\Pages\ListBeneficiaries;
use App\Filament\Resources\BeneficiaryResource\Pages\CreateBeneficiary;
use App\Filament\Resources\BeneficiaryResource\Pages\EditBeneficiary;
use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Filament\Resources\BeneficiaryResource\RelationManagers;
use App\Models\Beneficiary;
use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Usuarios';
    protected static ?string $label = 'Usuario';
    protected static string | \BackedEnum | null $navigationIcon = 'tabler-user-circle';
    protected static ?string $recordTitleAttribute = 'Usuarios';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Datos del Usuario')
                            ->icon('tabler-user')
                            ->schema([
                                Fieldset::make('Voluntario')
                                    ->schema([
                                        // voluntario y estado
                                        Select::make('volunteer_id')
                                            ->required()
                                            ->relationship('Volunteer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label('Voluntario'),
                                        Select::make('state')
                                            ->required()
                                            ->hiddenOn('create')
                                            ->default('Activo')
                                            ->label('Estado')
                                            ->options([
                                                'Activo' => 'Activo',
                                                'Pasivo' => 'Pasivo',
                                                'Archivado' => 'Archivado',
                                            ]),
                                    ]),
                                Fieldset::make('Datos Personales')
                                    ->schema([
                                        // datos del Usuario
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->validationMessages([
                                                'unique' => 'El :Attribute ya esta registrado.',
                                            ])
                                            ->label('Nombres y Apellidos'),
                                            TextInput::make('id')
                                            ->maxLength(255)
                                            ->label('Número')
                                            ->hiddenOn('create')
                                            ->readOnly(true),
                                        TextInput::make('expedient')
                                            ->maxLength(255)
                                            ->label('Nº de Expediente'),
                                        TextInput::make('dni')
                                            ->maxLength(255)
                                            ->label('DNI / NIE / PAS')
                                            ->unique(ignoreRecord: true)
                                            ->validationMessages([
                                                'unique' => 'El :Attribute ya esta registrado.',
                                            ]),
                                        DatePicker::make('expiration_date')
                                            ->label('Fecha de Vencimiento'),
                                        Country::make('nationality')
                                            ->label('Nacionalidad')
                                            ->searchable(),
                                        DatePicker::make('birth_date')
                                            ->label('Fecha de Nacimiento'),
                                        TextInput::make('address')
                                            ->label('Dirección')
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label('Número de Telefono')
                                            ->unique(ignoreRecord: true)
                                            ->validationMessages([
                                                'unique' => 'El :Attribute ya esta registrado.',
                                            ])
                                            ->tel()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Correo Electronico')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('education')
                                            ->label('Nivel de Educación')
                                            ->maxLength(255),
                                    ]),
                                Fieldset::make('Datos Socioeconomicos')
                                    ->schema([
                                        // datos socioeconomicos
                                        Select::make('housing_type')
                                            ->label('Tipo de Vivienda')
                                            ->options([
                                                'Propia' => 'Propia',
                                                'Alquilada' => 'Alquilada',
                                                'Familiar' => 'Familiar',
                                                'Cedida' => 'Cedida',
                                                'Ocupada' => 'Ocupada',
                                            ]),
                                        TextInput::make('incomes')
                                            ->label('Ingresos')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro'),
                                        TextInput::make('light')
                                            ->label('Luz')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro'),
                                        TextInput::make('water')
                                            ->label('Agua')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro'),
                                        TextInput::make('rent')
                                            ->label('Alquiler/Hipoteca')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro'),
                                        TextInput::make('community')
                                            ->label('Comunidad')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro'),
                                        TextInput::make('others')
                                            ->label('Otros')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro'),
                                    ]),
                            ]),
                        Tab::make('Documentos')
                            ->icon('tabler-file-type-doc')
                            ->schema([
                                Fieldset::make('Documentos Generales')
                                    ->schema([
                                        //documentos generales
                                        Toggle::make('family_book')
                                            ->label('Libro de Familia')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false),
                                        Toggle::make('rent_contract')
                                            ->label('Contrato de Alquiler')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false),
                                        DatePicker::make('census_emission_date')
                                            ->label('Padron Municipal'),
                                        TextInput::make('social_assistance_name')
                                            ->label('Asistente Social')
                                            ->maxLength(255),
                                    ]),
                                Fieldset::make('Informe de Vida Laboral')
                                    ->schema([
                                        //documentos ivl
                                        DatePicker::make('ivl_emission_date')
                                            ->label('Fecha de Emision'),
                                        DatePicker::make('ivl_alta_date')
                                            ->label('Fecha de Alta'),
                                        DatePicker::make('ivl_baja_date')
                                            ->label('Fecha de Baja'),
                                    ])
                                    ->columns(3),
                                Fieldset::make('Certificado de Pensionista')
                                    ->schema([
                                        //documentos cdp
                                        DatePicker::make('cdp_emission_date')
                                            ->label('Fecha de Emision'),
                                        Toggle::make('cdp_state')
                                            ->label('Negativo o Positivo')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false)
                                            ->live(),
                                        TextInput::make('cdp_amount')
                                            ->label('Monto')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro')
                                            ->visible(fn (Get $get): bool => $get('cdp_state')),
                                    ])
                                    ->columns(3),
                                Fieldset::make('SEPE')
                                    ->schema([
                                        //documentos sepe
                                        DatePicker::make('sepe_emission_date')
                                            ->label('Fecha de Emision'),
                                        Toggle::make('sepe_state')
                                            ->label('Negativo o Positivo')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false)
                                            ->live(),
                                        TextInput::make('sepe_amount')
                                            ->label('Monto')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro')
                                            ->visible(fn (Get $get): bool => $get('sepe_state')),
                                    ])
                                    ->columns(3),
                                Fieldset::make('Renta Minima Vital')
                                    ->schema([
                                        //documentos rmv
                                        DatePicker::make('rmv_emission_date')
                                            ->label('Fecha de Emision'),
                                        Toggle::make('rmv_state')
                                            ->label('Negativo o Positivo')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false)
                                            ->live(),
                                        TextInput::make('rmv_amount')
                                            ->label('Monto')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro')
                                            ->visible(fn (Get $get): bool => $get('rmv_state')),
                                    ])
                                    ->columns(3),
                                Fieldset::make('REMISA')
                                    ->schema([
                                        //documentos remisa
                                        DatePicker::make('remisa_emission_date')
                                            ->label('Fecha de Emision'),
                                        Toggle::make('remisa_state')
                                            ->label('Negativo o Positivo')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false)
                                            ->live(),
                                        TextInput::make('remisa_amount')
                                            ->label('Monto')
                                            ->numeric()
                                            ->inputMode('decimal')
                                            ->prefixIcon('heroicon-o-currency-euro')
                                            ->visible(fn (Get $get): bool => $get('remisa_state')),
                                    ])
                                    ->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nº')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('name')
                    ->label('Nombres y Apellidos')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expedient')
                    ->label('Exp')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('dni')
                    ->label('DNI / NIE / PAS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nationality')
                    ->label('Nacionalidad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label('Fecha de Nacimiento')
                    ->date()
                    ->sortable(),
                TextColumn::make('age')
                    ->label('Edad')
                    ->sortable()
                    ->state(function (Beneficiary $record): ?string {
                        return Carbon::parse($record->birth_date)->age;
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('education')
                    ->label('Nivel de Educación')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->options([
                        'Activo' => 'Activo',
                        'Pasivo' => 'Pasivo',
                        'Archivado' => 'Archivado',
                    ])
                    ->label('Estado')
                    ->attribute('state')
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('intervention')
                    ->label('Interv')
                    ->color('success')
                    ->icon('tabler-download')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo FacadePdf::loadHtml(
                                Blade::render('pdf.intervention', ['record' => $record])
                            )->stream();
                        }, 'Hoja de Intervención de ' . $record->name . '.pdf');
                    }),
                Action::make('data-protection')
                    ->label('Protec Datos')
                    ->color('info')
                    ->icon('tabler-user-shield')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo FacadePdf::loadHtml(
                                Blade::render('pdf.data-protection', ['record' => $record])
                            )->stream();
                        }, 'Hoja de Proteccion de Datos de ' . $record->name . '.pdf');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    /* ExportBulkAction::make()->exports([
                        ExcelExport::make()->fromTable(),
                        //export basico en excel para los datos de la tabla  
                    ]),*/
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FamilyRelationManager::class,
            RecordRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeneficiaries::route('/'),
            'create' => CreateBeneficiary::route('/create'),
            'edit' => EditBeneficiary::route('/{record}/edit'),
        ];
    }
}
