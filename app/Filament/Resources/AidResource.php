<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AidResource\Pages\CreateAid;
use App\Filament\Resources\AidResource\Pages\EditAid;
use App\Filament\Resources\AidResource\Pages\ListAids;
use App\Models\Aid;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Model2;
use Illuminate\Support\Facades\Blade;

class AidResource extends Resource
{
    protected static ?string $model = Aid::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Usuarios';

    protected static ?string $navigationLabel = 'Ayudas';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-coin-euro';

    protected static ?string $recordTitleAttribute = 'Ayudas';

    protected static ?string $label = 'Ayudas';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Titular y Voluntario')
                    ->schema([
                        Select::make('beneficiary_id')
                            ->relationship('beneficiary', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Usuario'),
                        Select::make('volunteer_id')
                            ->relationship('volunteer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Voluntario'),
                    ]),
                Fieldset::make('Ayuda')
                    ->schema([
                        Select::make('type')
                            ->label('Tipo de Ayuda')
                            ->required()
                            ->options([
                                'Lucha Contra la Pobreza Energética' => [
                                    'Pago de suministro' => 'Pago de suministro',
                                    'Mejora de aislamiento' => 'Mejora de aislamiento',
                                    'Adquisición y reposición de elementos luminosos de bajo consumo' => 'Adquisición y reposición de elementos luminosos de bajo consumo',
                                    'Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipamientos' => 'Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipamientos',
                                    'Otras necesidades básicas de energía' => 'Otras necesidades básicas de energía',
                                ],
                                'Gastos Relativos a la Vivienda' => [
                                    'Impago de alquiler' => 'Impago de alquiler',
                                    'Impago de crédito hipotecario' => 'Impago de crédito hipotecario',
                                    'Gastos derivados de las alternativas al alquiler' => 'Gastos derivados de las alternativas al alquiler',
                                    'Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipos NO relacionados con la eficiencia energética' => 'Adecuación, mejora, reparación y/o mantenimiento de instalaciones y equipos NO relacionados con la eficiencia energética',
                                    'Equipamiento básico del hogar' => 'Equipamiento básico del hogar',
                                    'Ropero (Ropa, Zapatos, Uniformes, Lencería del hogar, etc.)' => 'Ropero (Ropa, Zapatos, Uniformes, Lencería del hogar, etc.)',
                                    'Reparación de vehículo' => 'Reparación de vehículo',
                                    'Otras necesidades básicas de vivienda' => 'Otras necesidades básicas de vivienda',
                                ],
                                'Gastos Relativos a la Reducción de la Brecha Digital' => [
                                    'Pago de telefonía e internet' => 'Pago de telefonía e internet',
                                    'Equipamiento digital' => 'Equipamiento digital',
                                    'Otras necesidades básicas de la brecha digital' => 'Otras necesidades básicas de la brecha digital',
                                ],
                                'Gastos Relativos a la Educación y Formación' => [
                                    'Material escolar' => 'Material escolar',
                                    'Servicios escolares (Aula matinal, aula de mediodía, comedor, extraescolares, etc.)' => 'Servicios escolares (Aula matinal, aula de mediodía, comedor, extraescolares, etc.)',
                                    'Gastos de transporte' => 'Gastos de transporte',
                                    'Otras necesidades básicas de educación' => 'Otras necesidades básicas de educación',
                                ],
                                'Gastos Relativos a la Salud' => [
                                    'Material farmacéutico (fármacos, copagos, etc.)' => 'Material farmacéutico (fármacos, copagos, etc.)',
                                    'Óptica y ortopedia' => 'Óptica y ortopedia',
                                    'Odontología' => 'Odontología',
                                    'Servicios terapéuticos' => 'Servicios terapéuticos',
                                    'Otras necesidades básicas de salud' => 'Otras necesidades básicas de salud',
                                ],
                                'Otras Necesidades Básicas' => [
                                    'Alimentación e higiene' => 'Alimentación e higiene',
                                    'Gastos de transporte o viajes' => 'Gastos de transporte o viajes',
                                    'Otras necesidades básicas' => 'Otras necesidades básicas',
                                ],
                            ]),
                        Select::make('status')
                            ->label('Etapa')
                            ->options([
                                'En Estudio' => 'En Estudio',
                                'Aceptada' => 'Aceptada',
                                'Rechazada' => 'Rechazada',
                                'Terminada' => 'Terminada',
                            ]),
                        Select::make('collaborator_id')
                            ->relationship('collaborator', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Colaborador'),
                        Select::make('paid_by')
                            ->label('Pagado por Caritas')
                            ->options([
                                'Parroquial' => 'Parroquial',
                                'Diocesana' => 'Diocesana',
                            ]),
                    ]),
                Fieldset::make('Fecha y Cantidad')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        DatePicker::make('end_date')
                            ->label('Fecha de Fin'),
                        TextInput::make('approved_amount')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro')
                            ->label('Importe mensual'),
                        TextInput::make('total_amount')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro')
                            ->label('Importe Total'),
                    ]),
                Fieldset::make('Notas')
                    ->schema([
                        Textarea::make('notes')
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpanFull()
                            ->label('Notas'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipo de Ayuda')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Etapa')
                    ->searchable(),
                TextColumn::make('beneficiary.name')
                    ->searchable()
                    ->numeric()
                    ->sortable()
                    ->label('Usuario'),
                TextColumn::make('collaborator.name')
                    ->numeric()
                    ->sortable()
                    ->label('Colaborador'),
                TextColumn::make('paid_by')
                    ->numeric()
                    ->sortable()
                    ->label('Pagado Por Caritas'),
                TextColumn::make('volunteer.name')
                    ->numeric()
                    ->sortable()
                    ->label('Voluntario')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('Fecha de Inicio')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('Fecha de Fin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approved_amount')
                    ->numeric()
                    ->sortable()
                    ->label('Importe mensual')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable()
                    ->label('Importe Total')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->searchable()
                    ->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Creado')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'En Estudio' => 'En Estudio',
                        'Aceptada' => 'Aceptada',
                        'Rechazada' => 'Rechazada',
                        'Terminada' => 'Terminada',
                    ])
                    ->attribute('status')
                    ->label('Etapa')
                    ->multiple(),
                SelectFilter::make('collaborator')
                    ->relationship('collaborator', 'name', fn (Builder $query) => $query->whereIn('id', Aid::pluck('collaborator_id')->unique()))
                    ->searchable()
                    ->preload()
                    ->label('Colaborador'),
                SelectFilter::make('type')
                    ->options(
                        fn () => Aid::select('type')
                            ->distinct()
                            ->pluck('type', 'type')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->label('Tipo de Ayuda'),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('tabler-download')
                    ->action(function (Model2 $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo FacadePdf::loadHtml(
                                Blade::render('pdf.receipt', ['record' => $record])
                            )->stream();
                        }, 'Ayuda de '.$record->type.' a '.$record->Beneficiary->name.'.pdf');
                    }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAids::route('/'),
            'create' => CreateAid::route('/create'),
            'edit' => EditAid::route('/{record}/edit'),
        ];
    }
}
