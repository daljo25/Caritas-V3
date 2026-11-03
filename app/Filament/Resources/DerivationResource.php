<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\DerivationResource\Pages\ListDerivations;
use App\Filament\Resources\DerivationResource\Pages\CreateDerivation;
use App\Filament\Resources\DerivationResource\Pages\EditDerivation;
use App\Filament\Resources\DerivationResource\Pages;
use App\Filament\Resources\DerivationResource\RelationManagers;
use App\Models\Derivation;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;

class DerivationResource extends Resource
{
    protected static ?string $model = Derivation::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Usuarios';
    protected static ?string $label = 'Derivaciones';
    protected static string | \BackedEnum | null $navigationIcon = 'tabler-directions';
    protected static ?string $recordTitleAttribute = 'Derivaciones';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Titular y Voluntario')
                    ->schema([
                        Select::make('beneficiary_id')
                            ->label('Usuario')
                            ->relationship('beneficiary', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                        Select::make('volunteer_id')
                            ->label('Voluntario')
                            ->relationship('volunteer', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                    ]),
                Fieldset::make('Derivación')
                    ->schema([
                        Select::make('collaborator_id')
                            ->label('Colaborador')
                            ->relationship('collaborator', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Textarea::make('reason')
                            ->label('Motivo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('observation')
                            ->label('Observaciones')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('beneficiary.name')
                    ->label('Usuario')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('volunteer.name')
                    ->label('Voluntario')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('collaborator.name')
                    ->label('Colaborador')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('reason')
                    ->label('Motivo')
                    ->words(10),
                TextColumn::make('observation')
                    ->label('Observaciones')
                    ->words(10),
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
                Action::make('pdf') 
                    ->label('PDF')
                    ->color('success')
                    ->icon('tabler-download')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo FacadePdf::loadHtml(
                                Blade::render('pdf.derivation', ['record' => $record])
                            )->stream();
                        },'Derivacion de ' . $record->Beneficiary->name .' a '. $record->Collaborator->name . '.pdf');
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
            'index' => ListDerivations::route('/'),
            'create' => CreateDerivation::route('/create'),
            'edit' => EditDerivation::route('/{record}/edit'),
        ];
    }
}
