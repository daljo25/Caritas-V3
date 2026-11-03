<?php

namespace App\Filament\Resources\BeneficiaryResource\RelationManagers;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecordRelationManager extends RelationManager
{
    protected static string $relationship = 'Record';

    protected static ?string $label = 'Historial';

    protected static ?string $title = 'Historial';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('volunteer_id')
                    ->label('Voluntario')
                    ->required()
                    ->relationship('Volunteer', 'name'),
                DatePicker::make('date')
                    ->label('Fecha')
                    ->required()
                    ->default(Carbon::now()->format('d-m-Y')),
                RichEditor::make('incident')
                    ->label('Incidente')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('Volunteer.name')
                    ->label('Voluntario'),
                TextColumn::make('date')
                    ->label('Fecha'),
                TextColumn::make('incident')
                    ->label('Incidente')
                    ->words(10)
                    ->wrap()
                    ->html(),
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
