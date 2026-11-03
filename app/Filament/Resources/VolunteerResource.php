<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\VolunteerResource\Pages\ManageVolunteers;
use App\Filament\Resources\VolunteerResource\Pages;
use App\Filament\Resources\VolunteerResource\RelationManagers;
use App\Models\Volunteer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VolunteerResource extends Resource
{
    protected static ?string $model = Volunteer::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Registro de datos';
    protected static ?string $label = 'Voluntarios';
    protected static string | \BackedEnum | null $navigationIcon = 'tabler-mood-heart';
    protected static ?string $recordTitleAttribute = 'Voluntarios';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombres y Apellidos')
                    ->required()
                    ->maxLength(255),
                TextInput::make('dni')
                    ->label('DNI / NIE / PAS')
                    ->maxLength(255),
                DatePicker::make('birth_date')
                    ->label('Fecha de Nacimiento'),
                Select::make('gender')
                    ->label('Género')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                    ]),
                TextInput::make('address')
                    ->label('Dirección')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label('Notas')
                    ->maxLength(65535),
                FileUpload::make('image')
                    ->label('Imagen')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombres y Apellidos')
                    ->searchable()
                    ->sortable(),
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
                    ->state(function (Volunteer $record): ?string {
                        return Carbon::parse($record->birth_date)->age;
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('gender')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Imagen'),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageVolunteers::route('/'),
        ];
    }
}
