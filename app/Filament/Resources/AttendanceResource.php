<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Filament\Forms;
use Filament\Forms\Form;
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
    protected static ?string $navigationGroup = 'Usuarios';
    protected static ?string $label = 'Certificado de asistencia';
    protected static ?string $pluralLabel = 'Certificados de asistencia';
    protected static ?string $navigationLabel = 'Certificados de Asistencia';
    protected static ?string $navigationIcon = 'tabler-certificate';
    protected static ?string $recordTitleAttribute = 'Asistencia';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('certificate_number')
                    ->label('Número de certificado')
                    ->placeholder('Escribe el número de certificado')
                    ->hiddenOn('create')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('beneficiary_id')
                    ->relationship('beneficiary', 'name')
                    ->label('Usuario')
                    ->required()
                    ->preload()
                    ->searchable(),
                Forms\Components\DatePicker::make('attendance_date')
                    ->label('Fecha de asistencia')
                    ->required()
                    ->default(now())
                    ->minDate(now()->subYear(1))
                    ->maxDate(now()->addYear(1))
                    ->placeholder('Selecciona una fecha'),
                Forms\Components\TimePicker::make('attendance_time')
                    ->label('Hora de asistencia')
                    ->default(now())
                    ->placeholder('Selecciona una hora')
                    ->seconds(false)
                    ->format('H:i')
                    ->required(),
                Forms\Components\Textarea::make('purpose')
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
                Tables\Columns\TextColumn::make('certificate_number')
                    ->label('Número de certificado')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('beneficiary.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Fecha de asistencia')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_time')
                    ->label('Hora de asistencia')
                    ->time(format: 'h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf') 
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
