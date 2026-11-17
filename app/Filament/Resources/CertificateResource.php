<?php

namespace App\Filament\Resources;

use AmjadIqbal\FilamentRecordPdf\Actions\PreviewPdfAction;
use App\Filament\Resources\CertificateResource\Pages\CreateCertificate;
use App\Filament\Resources\CertificateResource\Pages\EditCertificate;
use App\Filament\Resources\CertificateResource\Pages\ListCertificates;
use App\Models\Certificate;
use App\Models\Family;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Usuarios';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-certificate';

    protected static ?string $label = 'Certificado';

    protected static ?string $pluralLabel = 'Certificados';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Destinatario')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('recipient_type')
                            ->label('¿Para quién es?')
                            ->options(Certificate::recipientTypes())
                            ->default('beneficiario')
                            ->required()
                            ->live(),
                        TextInput::make('recipient_name')
                            ->label('Nombre y apellidos')
                            ->maxLength(255)
                            ->helperText('Para personas que no están en la base de datos.')
                            ->visible(fn (Get $get): bool => $get('recipient_type') === 'externo')
                            ->required(fn (Get $get): bool => $get('recipient_type') === 'externo'),
                        Select::make('beneficiary_id')
                            ->label('Beneficiario')
                            ->relationship('beneficiary', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('recipient_type') === 'beneficiario')
                            ->required(fn (Get $get): bool => $get('recipient_type') === 'beneficiario'),
                        Select::make('family_id')
                            ->label('Familiar')
                            ->relationship(
                                'family',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->with('Beneficiary'),
                            )
                            ->getOptionLabelFromRecordUsing(fn (Family $record): string => filled($record->Beneficiary?->name)
                                ? "{$record->name} — {$record->Beneficiary->name}"
                                : $record->name)
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => $get('recipient_type') === 'familiar')
                            ->required(fn (Get $get): bool => $get('recipient_type') === 'familiar'),
                    ]),
                Fieldset::make('Documento')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->datalist([
                                'Certificado',
                                'Solicitud',
                                'Autorización',
                            ]),
                        Textarea::make('body')
                            ->label('Texto del documento')
                            ->required()
                            ->rows(12)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('recipient_name')
                    ->label('Destinatario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recipient_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): ?string => Certificate::recipientTypes()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('recipient_type')
                    ->label('Tipo')
                    ->options(Certificate::recipientTypes()),
            ])
            ->recordActions([
                PreviewPdfAction::make()
                    ->label('Imprimir')
                    ->icon('tabler-printer')
                    ->pdfView('pdf.certificate')
                    ->pdfFilename(fn (Certificate $record): string => $record->title.' de '.$record->recipient_name),
                EditAction::make(),
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
            'index' => ListCertificates::route('/'),
            'create' => CreateCertificate::route('/create'),
            'edit' => EditCertificate::route('/{record}/edit'),
        ];
    }
}
