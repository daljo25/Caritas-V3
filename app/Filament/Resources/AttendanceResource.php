<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use App\Filament\Resources\AttendanceResource\Pages\CreateAttendance;
use App\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Usuarios';
    protected static ?string $label = 'Certificado de asistencia';
    protected static ?string $pluralLabel = 'Certificados de asistencia';
    protected static ?string $navigationLabel = 'Certificados de Asistencia';
    protected static string | \BackedEnum | null $navigationIcon = 'tabler-certificate';
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
                Action::make('pdf') 
                    ->label('PDF')
                    ->color('success')
                    ->icon('tabler-download')
                    ->action(function (Model $attendance) {
                        return response()->streamDownload(function () use ($attendance) {
                            echo FacadePdf::loadHtml(
                                Blade::render('pdf.attendance', ['attendance' => $attendance])
                            )->stream();
                        },"certificado-asistencia-{$attendance->certificate_number}.pdf");
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
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
