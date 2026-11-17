<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkExperienceResource\Pages\CreateWorkExperience;
use App\Filament\Resources\WorkExperienceResource\Pages\EditWorkExperience;
use App\Filament\Resources\WorkExperienceResource\Pages\ListWorkExperiences;
use App\Models\WorkExperience;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkExperienceResource extends Resource
{
    protected static ?string $model = WorkExperience::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Registro de datos';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-briefcase';

    protected static ?string $label = 'Experiencia laboral';

    protected static ?string $pluralLabel = 'Experiencias laborales';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Experiencia')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Experiencia')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('beneficiaries_count')
                    ->label('Beneficiarios')
                    ->counts('beneficiaries')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('families_count')
                    ->label('Familiares')
                    ->counts('families')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('beneficiaries')
                    ->label('Ver beneficiarios')
                    ->icon('tabler-user-search')
                    ->url(fn (WorkExperience $record): string => BeneficiaryResource::getUrl('index', [
                        'filters' => ['workExperiences' => ['values' => [$record->id]]],
                    ])),
                Action::make('families')
                    ->label('Ver familiares')
                    ->icon('tabler-users-group')
                    ->url(fn (WorkExperience $record): string => FamilyResource::getUrl('index', [
                        'filters' => ['workExperiences' => ['values' => [$record->id]]],
                    ])),
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ListWorkExperiences::route('/'),
            'create' => CreateWorkExperience::route('/create'),
            'edit' => EditWorkExperience::route('/{record}/edit'),
        ];
    }
}
