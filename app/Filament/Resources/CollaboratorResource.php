<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollaboratorResource\Pages\ManageCollaborators;
use App\Models\Collaborator;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CollaboratorResource extends Resource
{
    protected static ?string $model = Collaborator::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Registro de datos';

    protected static ?string $label = 'Colaboradores';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-building';

    protected static ?string $recordTitleAttribute = 'Colaboradores';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Datos de la Empresa u Organizmo')
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('address')
                            ->maxLength(255)
                            ->label('Dirección'),
                        TextInput::make('website')
                            ->maxLength(255)
                            ->label('Sitio web')
                            ->url(),
                        Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Imagen')
                            ->image()
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('Persona Responsable')
                    ->schema([
                        TextInput::make('responsable')
                            ->maxLength(255)
                            ->label('Nombres y Apellidos'),
                        TextInput::make('position')
                            ->maxLength(255)
                            ->label('Cargo'),
                        TextInput::make('email')
                            ->maxLength(255)
                            ->label('Correo electrónico')
                            ->email(),
                        TextInput::make('phone')
                            ->maxLength(255)
                            ->label('Telefono')
                            ->tel(),
                    ]),
                Fieldset::make('Otro Contacto')
                    ->schema([
                        TextInput::make('responsable2')
                            ->maxLength(255)
                            ->label('Nombres y Apellidos'),
                        TextInput::make('position2')
                            ->maxLength(255)
                            ->label('Cargo'),
                        TextInput::make('email2')
                            ->maxLength(255)
                            ->label('Correo electrónico')
                            ->email(),
                        TextInput::make('phone2')
                            ->maxLength(255)
                            ->label('Telefono')
                            ->tel(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website')
                    ->label('Sitio Web')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('image')
                    ->label('Imagen'),
                TextColumn::make('responsable')
                    ->label('Responsable')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('position')
                    ->label('Cargo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Correo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('responsable2')
                    ->label('Responsable')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('position2')
                    ->label('Cargo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email2')
                    ->label('Correo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone2')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Notas')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(20),
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
            'index' => ManageCollaborators::route('/'),
        ];
    }
}
