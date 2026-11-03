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
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\FamilyResource\Pages\ListFamilies;
use App\Filament\Resources\FamilyResource\Pages\CreateFamily;
use App\Filament\Resources\FamilyResource\Pages\EditFamily;
use App\Filament\Resources\FamilyResource\Pages;
use App\Filament\Resources\FamilyResource\RelationManagers;
use App\Models\Family;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;

class FamilyResource extends Resource
{
    protected static ?string $model = Family::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Usuarios';
    protected static ?string $label = 'Familiares';
    protected static string | \BackedEnum | null $navigationIcon = 'tabler-users-group';
    protected static ?string $recordTitleAttribute = 'Familiares';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Datos del Familiar')
                            ->icon('tabler-user')
                            ->schema([
                                Fieldset::make('Usuario Titular')
                                    ->schema([
                                        // titular
                                        Select::make('beneficiary_id')
                                            ->label('Usuario Titular')
                                            ->relationship('beneficiary', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                    ]),
                                Fieldset::make('Datos Personales')
                                    ->schema([
                                        // datos del familiar
                                        TextInput::make('name')
                                            ->label('Nombres y Apellidos')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('dni')
                                            ->label('DNI / NIE / PAS')
                                            ->maxLength(255),
                                        DatePicker::make('expiration_date')
                                            ->label('Fecha de Vencimiento'),
                                        Country::make('nationality')
                                            ->label('Nacionalidad')
                                            ->searchable(),
                                        DatePicker::make('birth_date')
                                            ->label('Fecha de Nacimiento'),
                                        Select::make('relationship')
                                            ->label('Parentesco')
                                            ->options([
                                                'Esposo/a' => 'Esposo/a',
                                                'Hijo' => 'Hijo',
                                                'Hija' => 'Hija',
                                                'Madre' => 'Madre',
                                                'Padre' => 'Padre',
                                                'Hermano/a' => 'Hermano/a',
                                                'Primo/a' => 'Primo/a',
                                                'Tio/a' => 'Tio/a',
                                                'Sobrino/a' => 'Sobrino/a',
                                                'Otro' => 'Otro',
                                            ]),
                                        TextInput::make('phone')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Correo Electrónico')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('education')
                                            ->label('Nivel de Educación')
                                            ->maxLength(255),
                                    ])
                            ])
                            ->columns(2),
                        Tab::make('Documentos')
                            ->icon('tabler-file-type-doc')
                            ->schema([
                                Fieldset::make('Informe de Vida Laboral')
                                    ->schema([
                                        //documentos ivl
                                        DatePicker::make('ivl_emission_date')
                                            ->label('Fecha de Emisión'),
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
                                            ->label('Fecha de Emisión'),
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
                                            ->label('Fecha de Emisión'),
                                        Toggle::make('sepe_state')
                                            ->label('Negativo o Positivo')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->inline(false)
                                            ,
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
                                            ->label('Fecha de Emisión'),
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
                                            ->label('Fecha de Emisión'),
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
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('beneficiary.name')
                    ->label('Usuario Titular')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombres y Apellidos')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dni')
                    ->label('DNI / NIE / PAS')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Fecha de Nacimiento')
                    ->date()
                    ->sortable(),
                    TextColumn::make('age')
                    ->label('Edad')
                    ->sortable()
                    ->state(function (Family $record): ?string{
                        return Carbon::parse($record->birth_date)->age;
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('relationship')
                    ->label('Parentesco')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                TextColumn::make('education')
                    ->label('Nivel de Educación')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListFamilies::route('/'),
            'create' => CreateFamily::route('/create'),
            'edit' => EditFamily::route('/{record}/edit'),
        ];
    }
}
