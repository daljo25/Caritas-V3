<?php

namespace App\Filament\Resources;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\MonthlyReportResource\Pages\CreateMonthlyReport;
use App\Filament\Resources\MonthlyReportResource\Pages\EditMonthlyReport;
use App\Filament\Resources\MonthlyReportResource\Pages\ListMonthlyReports;
use App\Models\MonthlyReport;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MonthlyReportResource extends Resource
{
    protected static ?string $model = MonthlyReport::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Administracion';

    protected static ?string $label = 'Reporte Mensual';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-calendar-month';

    protected static ?string $recordTitleAttribute = 'Reporte Mensual';

    protected static ?string $pluralLabel = 'Reportes Mensuales';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Fecha del Reporte')
                    ->schema([
                        Select::make('month')
                            ->label('Mes')
                            ->required()
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
                        Select::make('year')
                            ->label('Año')
                            ->required()
                            ->options([
                                '2024' => '2024',
                                '2025' => '2025',
                                '2026' => '2026',
                                '2027' => '2027',
                            ]),
                    ]),
                Fieldset::make('Ingresos')
                    ->schema([
                        TextInput::make('collection')
                            ->numeric()
                            ->label('Colectas')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('parroquial_receipt')
                            ->numeric()
                            ->label('Recibos de Socios Cobrados')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('bank_donation')
                            ->numeric()
                            ->label('Donativos por Banco')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('volunteer_campaign_donation')
                            ->numeric()
                            ->label('Campañas (Socios/Voluntarios)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('diosesano_receipt')
                            ->numeric()
                            ->label('Recibos Cobrados por Caritas Diosesana')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('diosesano_donation')
                            ->numeric()
                            ->label('Dinero recibido de Caritas Diosesana')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('other_donation')
                            ->numeric()
                            ->label('Recibido de Otras Entidades')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('special_donation')
                            ->numeric()
                            ->label('Donaciones en Especie')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                    ]),
                Fieldset::make('Gastos')
                    ->schema([
                        TextInput::make('transfer_collection')
                            ->label('Transferencia al Fondo Comun Diosesano por Colectas')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('transfer_membership')
                            ->label('Transferencia al Fondo Comun Diosesano por Recibos de Socios')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('transfer_campaign')
                            ->label('Transferencia al Fondo Comun Diosesano por Campañas')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('transfer_other')
                            ->label('Transferencia al Fondo Comun Diosesano por Otros Conceptos')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('transfer_arciprestal')
                            ->label('Transferencia al Arciprestal')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('health')
                            ->numeric()
                            ->label('Gastos de Salud (medicamentos, ortopedia, optica, ortodoncia, etc)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('housing')
                            ->numeric()
                            ->label('Gastos en Vivienda (alquileres, hipotecas, equipamiento de vivienda)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('food')
                            ->numeric()
                            ->label('Gastos para Alimentacion e Higiene Personal')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('supplies_receipt')
                            ->numeric()
                            ->label('Gastos de Recibos de Suministros Basicos (comunidad, electricidad, agua, gas)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('other_intervention')
                            ->numeric()
                            ->label('Otras Intervenciones (material escolar, proyectos de trabajo, bonobus, etc)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('parish_project')
                            ->numeric()
                            ->label('Proyectos Especificos Parroquiales')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('general_expense')
                            ->numeric()
                            ->label('Gastos Generales Internos (mantenimiento, banco, etc)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('other_entity')
                            ->numeric()
                            ->label('Transferencias a Otras Entidades')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('campaign_volunteers')
                            ->numeric()
                            ->label('Campañas (Socios/Voluntarios)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('campaign_local_emergency')
                            ->numeric()
                            ->label('Campañas (Emergencias Locales)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('campaign_international_emergency')
                            ->numeric()
                            ->label('Campañas (Emergencias Internacionales)')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),
                        TextInput::make('development_cooperation')
                            ->numeric()
                            ->label('Cooperación al Desarrollo')
                            ->inputMode('decimal')
                            ->prefixIcon('heroicon-o-currency-euro'),

                    ]),
                Fieldset::make('Mensaje')
                    ->schema([
                        RichEditor::make('message')
                            ->label('Mensaje')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('month')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('message')
                    ->label('Incidente')
                    ->words(20)
                    ->wrap()
                    ->html(),
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
                PreviewPdfAction::make()
                    ->label('PDF')
                    ->color('success')
                    ->icon('tabler-eye')
                    ->pdfView('pdf.monthly-report')
                    ->pdfFilename(fn (Model $record): string => 'Comunicado Mensual mes '.$record->month.' año '.$record->year),
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
            'index' => ListMonthlyReports::route('/'),
            'create' => CreateMonthlyReport::route('/create'),
            'edit' => EditMonthlyReport::route('/{record}/edit'),
        ];
    }
}
