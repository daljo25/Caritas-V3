<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftCardResource\Pages\CreateGiftCard;
use App\Filament\Resources\GiftCardResource\Pages\EditGiftCard;
use App\Filament\Resources\GiftCardResource\Pages\ListGiftCards;
use App\Filament\Widgets\GiftCardStats;
use App\Models\Aid;
use App\Models\GiftCard;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Registro de datos';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-credit-card';

    protected static ?string $label = 'Tarjeta de regalo';

    protected static ?string $pluralLabel = 'Tarjetas de regalo';

    protected static ?string $recordTitleAttribute = 'Tarjetas de regalo';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('serie')
                    ->label('Serie')
                    ->required()
                    ->maxLength(50),
                TextInput::make('number')
                    ->label('Número')
                    ->required()
                    ->maxLength(50),
                TextInput::make('pin')
                    ->label('PIN')
                    ->required()
                    ->maxLength(50),
                TextInput::make('amount')
                    ->label('Monto')
                    ->prefixIcon('heroicon-o-currency-euro')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal'),
                Select::make('aid_id')
                    ->label('Ayuda')
                    ->searchable()
                    ->options(function () {
                        return Aid::query()
                            ->with('beneficiary') // Cargar la relación `beneficiary`
                            ->where('status', 'Aceptada') // Filtrar solo los Aid con status = 'Aceptada'
                            ->get()
                            ->pluck('beneficiary.name', 'id') // Obtener el nombre del Beneficiary y el id del Aid
                            ->mapWithKeys(function ($name, $id) {
                                return [$id => "{$id} - {$name}"]; // Formatear la salida: "id - nombre"
                            })
                            ->toArray();
                    })
                    ->getSearchResultsUsing(function (string $search) {
                        return Aid::query()
                            ->with('beneficiary') // Cargar la relación `beneficiary`
                            ->where('status', 'Aceptada') // Filtrar solo los Aid con status = 'Aceptada'
                            ->where(function ($query) use ($search) {
                                $query->where('id', 'like', "%{$search}%") // Buscar por `id` del Aid
                                    ->orWhereHas('beneficiary', function ($query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%"); // Buscar por nombre del Beneficiary
                                    });
                            })
                            ->limit(50) // Limitar resultados para mejorar el rendimiento
                            ->get()
                            ->pluck('beneficiary.name', 'id') // Obtener el nombre del Beneficiary y el id del Aid
                            ->mapWithKeys(function ($name, $id) {
                                return [$id => "{$id} - {$name}"]; // Formatear la salida: "id - nombre"
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $aid = Aid::with('beneficiary')->find($value); // Buscar el Aid con su Beneficiary

                        return $aid ? "{$aid->id} - {$aid->beneficiary->name}" : null; // Formatear la salida: "id - nombre"
                    })
                    ->preload(),
                TextInput::make('issuer')
                    ->label('Emisor')
                    ->maxLength(255),
                TextInput::make('exp')
                    ->label('Expiración')
                    ->maxLength(20),
                DatePicker::make('delivery_date')
                    ->label('Fecha de entrega'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serie')
                    ->label('Serie')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('number')
                    ->label('Número')
                    ->sortable(),
                TextColumn::make('pin')
                    ->label('PIN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->prefix('€ ')
                    ->label('Monto')
                    ->numeric(locale: 'es-ES', decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('aid.id')
                    ->label('Ayuda')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('aid.beneficiary.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('issuer')
                    ->label('Emisor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('exp')
                    ->label('Expiración'),
                TextColumn::make('delivery_date')
                    ->label('Fecha de entrega')
                    ->date()
                    ->sortable(),
                IconColumn::make('aid_status')
                    ->label('¿Entregada?')
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! is_null($record->aid_id))
                    ->trueIcon('tabler-circle-check')
                    ->falseIcon('tabler-circle-x')
                    ->trueColor('success')
                    ->falseColor('danger'),
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
            'index' => ListGiftCards::route('/'),
            'create' => CreateGiftCard::route('/create'),
            'edit' => EditGiftCard::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            GiftCardStats::class,
        ];
    }
}
