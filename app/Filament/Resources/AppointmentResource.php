<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages\CreateAppointment;
use App\Filament\Resources\AppointmentResource\Pages\EditAppointment;
use App\Filament\Resources\AppointmentResource\Pages\ListAppointments;
use App\Models\Appointment;
use App\Models\Beneficiary;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Citas';

    protected static ?string $label = 'Cita';

    protected static ?string $pluralLabel = 'Citas';

    protected static ?string $navigationLabel = 'Citas';

    protected static ?string $recordTitleAttribute = 'Cita';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Usuario')
                    ->schema([
                        Select::make('beneficiary_id')
                            ->relationship('beneficiary', 'name')
                            ->label('Nombre')
                            ->searchable(['name', 'id', 'expedient', 'dni', 'phone'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $beneficiary = Beneficiary::find($state);
                                    if ($beneficiary) {
                                        $set('phone_number', $beneficiary->phone);
                                        $set('email', $beneficiary->email);
                                        $set('address', $beneficiary->address);
                                    }
                                } else {
                                    $set('phone_number', '');
                                    $set('email', '');
                                    $set('address', '');
                                }
                            })
                            ->createOptionForm([
                                TextInput::make('dni')
                                    ->label('DNI/NIE/PAS')
                                    ->required()
                                    ->unique()
                                    ->validationMessages([
                                        'unique' => 'El :Attribute ya esta registrado.',
                                    ])
                                    ->maxLength(255),
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->unique()
                                    ->validationMessages([
                                        'unique' => 'El :Attribute ya esta registrado.',
                                    ])
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->required()
                                    ->unique()
                                    ->validationMessages([
                                        'unique' => 'El :Attribute ya esta registrado.',
                                    ])
                                    ->maxLength(255),
                                TextInput::make('address')
                                    ->label('Dirección')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('volunteer_id')
                                    ->required()
                                    ->relationship('Volunteer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Voluntario'),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('state')
                                    ->label('Estado')
                                    ->required()
                                    ->default('Archivado')
                                    ->readOnly(),
                            ]),
                        TextInput::make('phone_number')
                            ->label('Teléfono')
                            ->tel()
                            ->readOnly()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->afterStateHydrated(function (callable $set, $state, $record) {
                                if ($record) {
                                    $beneficiary = $record->beneficiary;
                                    if ($beneficiary) {
                                        $set('phone_number', $beneficiary->phone);
                                    }
                                }
                            }),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->readOnly()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->afterStateHydrated(function (callable $set, $state, $record) {
                                if ($record) {
                                    $beneficiary = $record->beneficiary;
                                    if ($beneficiary) {
                                        $set('email', $beneficiary->email);
                                    }
                                }
                            }),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->readOnly()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->afterStateHydrated(function (callable $set, $state, $record) {
                                if ($record) {
                                    $beneficiary = $record->beneficiary;
                                    if ($beneficiary) {
                                        $set('address', $beneficiary->address);
                                    }
                                }
                            }),
                    ])
                    ->columns(2),
                Fieldset::make('Cita')
                    ->schema([
                        DatePicker::make('appointment_date')
                            ->label('Fecha de la Cita')
                            ->hiddenOn('create'),
                        TimePicker::make('appointment_time')
                            ->label('Hora de la Cita')
                            ->hiddenOn('create')
                            ->seconds(false)
                            ->datalist([
                                '10:00' => '10:00',
                                '10:30' => '10:30',
                                '11:00' => '11:00',
                                '11:30' => '11:30',
                                '12:00' => '12:00',
                                '12:30' => '12:30',
                                '13:00' => '13:00',
                                '13:30' => '13:30',
                            ]),
                        TextInput::make('notes')
                            ->label('Notas')
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'Aceptada' => 'Aceptada',
                                'Cancelada' => 'Cancelada',
                                'No Contesta' => 'No Contesta',
                                'Documentos Incompletos' => 'Documentos Incompletos',
                                'Rechazada' => 'Rechazada',
                                'Atendida' => 'Atendida',
                            ])
                            ->default('Pendiente'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('beneficiary.name')
                    ->searchable()
                    ->label('Usuario')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('appointment_date')
                    ->label('Fecha de la Cita')
                    ->date()
                    ->sortable(),
                TextColumn::make('appointment_time')
                    ->sortable()
                    ->label('Hora de la Cita'),
                TextColumn::make('notes')
                    ->searchable()
                    ->label('Notas'),
                TextColumn::make('status')
                    ->searchable()
                    ->label('Estado'),
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
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}
