<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonorResource\Pages\CreateDonor;
use App\Filament\Resources\DonorResource\Pages\EditDonor;
use App\Filament\Resources\DonorResource\Pages\ListDonors;
use App\Filament\Resources\DonorResource\RelationManagers\DonationRelationManager;
use App\Models\Donor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonorResource extends Resource
{
    protected static ?string $model = Donor::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Registro de datos';

    protected static ?string $label = 'Donante';

    protected static ?string $pluralLabel = 'Donantes';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-cash';

    protected static ?string $recordTitleAttribute = 'Donantes';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre'),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->label('Email'),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->label('Teléfono'),
                TextInput::make('address')
                    ->maxLength(255)
                    ->label('Dirección'),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->label('Notas'),
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
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Dirección')
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
            DonationRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonors::route('/'),
            'create' => CreateDonor::route('/create'),
            'edit' => EditDonor::route('/{record}/edit'),
        ];
    }
}
