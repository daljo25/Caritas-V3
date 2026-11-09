<?php

namespace App\Filament\Resources;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\AttendanceResource\Pages\CreateAttendance;
use App\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use App\Models\Attendance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Usuarios';

    protected static ?string $label = 'Certificado de asistencia';

    protected static ?string $pluralLabel = 'Certificados de asistencia';

    protected static ?string $navigationLabel = 'Certificados de Asistencia';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-certificate';

    protected static ?string $recordTitleAttribute = 'Asistencia';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('certificate_number')
                    ->label('Número de certificado')
                    ->placeholder('Escribe el número de certificado')
                    ->hiddenOn('create')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Select::make('beneficiary_id')
                    ->relationship('beneficiary', 'name')
                    ->label('Usuario')
                    ->required()
                    ->preload()
                    ->searchable(),
                DatePicker::make('attendance_date')
                    ->label('Fecha de asistencia')
                    ->required()
                    ->default(now())
                    ->minDate(now()->subYear(1))
                    ->maxDate(now()->addYear(1))
                    ->placeholder('Selecciona una fecha'),
                TimePicker::make('attendance_time')
                    ->label('Hora de asistencia')
                    ->default(now())
                    ->placeholder('Selecciona una hora')
                    ->seconds(false)
                    ->format('H:i')
                    ->required(),
                Textarea::make('purpose')
                    ->label('Motivo')
                    ->maxLength(255)
                    ->placeholder('Escribe el motivo de la asistencia')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_number')
                    ->label('Número de certificado')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('beneficiary.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->numeric(),
                TextColumn::make('attendance_date')
                    ->label('Fecha de asistencia')
                    ->date()
                    ->sortable(),
                TextColumn::make('attendance_time')
                    ->label('Hora de asistencia')
                    ->time(format: 'h:i A')
                    ->sortable(),
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
                    ->pdfView('pdf.attendance')
                    ->pdfData(fn (Model $attendance): array => ['attendance' => $attendance])
                    ->pdfFilename(fn (Model $attendance): string => "certificado-asistencia-{$attendance->certificate_number}"),
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
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
