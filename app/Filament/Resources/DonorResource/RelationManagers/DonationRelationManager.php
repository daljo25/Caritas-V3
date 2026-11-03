<?php

namespace App\Filament\Resources\DonorResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonationRelationManager extends RelationManager
{
    protected static string $relationship = 'Donation';

    protected static ?string $label = 'Donación';

    protected static ?string $pluralLabel = 'Donaciones';

    protected static ?string $title = 'Donaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('source')
                    ->required()
                    ->label('Medio')
                    ->options([
                        'Efectivo' => 'Efectivo',
                        'Transferencia Bancaria' => 'Transferencia Bancaria',
                    ]),
                Select::make('donation_month')
                    ->required()
                    ->label('Mes')
                    ->options([
                        '01' => 'Enero',
                        '02' => 'Febrero',
                        '03' => 'Marzo',
                        '04' => 'Abril',
                        '05' => 'Mayo',
                        '06' => 'Junio',
                        '07' => 'Julio',
                        '08' => 'Agosto',
                        '09' => 'Septiembre',
                        '10' => 'Octubre',
                        '11' => 'Noviembre',
                        '12' => 'Diciembre',
                    ]),
                Select::make('donation_year')
                    ->required()
                    ->label('Año')
                    ->options([
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                        '2027' => '2027',
                        '2028' => '2028',
                        '2029' => '2029',
                        '2030' => '2030',
                    ]),
                TextInput::make('amount')
                    ->required()
                    ->label('Cantidad')
                    ->inputMode('decimal')
                    ->prefixIcon('heroicon-o-currency-euro')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('source')
                    ->label('Medio')
                    ->sortable(),
                TextColumn::make('donation_month')
                    ->label('Mes')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('donation_year')
                    ->label('Año')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Cantidad'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
}
