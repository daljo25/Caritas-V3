<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AidResource\Pages;
use App\Filament\Resources\AidResource\RelationManagers;
use App\Models;
use App\Models\Aid;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as Model2;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;

class AidResource extends Resource
{
    protected static ?string $model = Aid::class;
    protected static ?string $navigationGroup = 'Usuarios';
    protected static ?string $navigationLabel = 'Ayudas';
    protected static ?string $navigationIcon = 'tabler-coin-euro';
    protected static ?string $recordTitleAttribute = 'Ayudas';
    protected static ?string $label = 'Ayudas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Titular y Voluntario')
                    ->schema([
                        Forms\Components\Select::make('beneficiary_id')
                            ->relationship('beneficiary', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Usuario'),
                        Forms\Components\Select::make('volunteer_id')
                            ->relationship('volunteer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Voluntario'),
                    ]),
                Fieldset::make('Ayuda')
                    ->schema([
                        Forms\Components\Select::make('type')
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
                                ]
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('Etapa')
                            ->options([
                                'En Estudio' => 'En Estudio',
                                'Aceptada' => 'Aceptada',
                                'Rechazada' => 'Rechazada',
                                'Terminada' => 'Terminada',
                            ]),
                        Forms\Components\Select::make('collaborator_id')
                            ->relationship('collaborator', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Colaborador'),
                        Forms\Components\Select::make('paid_by')
                            ->label('Pagado por Caritas')
                            ->options([
                                'Parroquial' => 'Parroquial',
                                'Diocesana' => 'Diocesana',
                            ]),
                    ]),
                Fieldset::make('Fecha y Cantidad')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin'),
                        Forms\Components\TextInput::make('approved_amount')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro')
                            ->label('Importe mensual'),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro')
                            ->label('Importe Total'),
                    ]),
                Fieldset::make('Notas')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de Ayuda')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Etapa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('beneficiary.name')
                    ->searchable()
                    ->numeric()
                    ->sortable()
                    ->label('Usuario'),
                Tables\Columns\TextColumn::make('collaborator.name')
                    ->numeric()
                    ->sortable()
                    ->label('Colaborador'),
                Tables\Columns\TextColumn::make('paid_by')
                    ->numeric()
                    ->sortable()
                    ->label('Pagado Por Caritas'),
                Tables\Columns\TextColumn::make('volunteer.name')
                    ->numeric()
                    ->sortable()
                    ->label('Voluntario')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('Fecha de Inicio')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('Fecha de Fin')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_amount')
                    ->numeric()
                    ->sortable()
                    ->label('Importe mensual')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable()
                    ->label('Importe Total')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable()
                    ->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Creado')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Actualizado')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'En Estudio' => 'En Estudio',
                        'Aceptada' => 'Aceptada',
                        'Rechazada' => 'Rechazada',
                        'Terminada' => 'Terminada',
                    ])
                    ->attribute('status')
                    ->label('Etapa')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('collaborator')
                    ->relationship('collaborator', 'name', fn (Builder $query) => $query->whereIn('id', Aid::pluck('collaborator_id')->unique()))
                    ->searchable()
                    ->preload()
                    ->label('Colaborador'),
                Tables\Filters\SelectFilter::make('type')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('tabler-download')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo FacadePdf::loadHtml(
                                Blade::render('pdf.receipt', ['record' => $record])
                            )->stream();
                        }, 'Ayuda de ' . $record->type . ' a ' . $record->Beneficiary->name . '.pdf');
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAids::route('/'),
            'create' => Pages\CreateAid::route('/create'),
            'edit' => Pages\EditAid::route('/{record}/edit'),
        ];
    }
}
