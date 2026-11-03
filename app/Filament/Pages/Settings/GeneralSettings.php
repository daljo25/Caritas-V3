<?php
namespace App\Filament\Pages\Settings;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Outerweb\FilamentSettings\Pages\Settings as BaseSettings;

class GeneralSettings extends BaseSettings
{
    public static function getNavigationLabel(): string
    {
        return 'Configuración General';
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Configuración';
    }
    public static function getNavigationIcon(): string
    {
        return 'tabler-settings';
    }
    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public function getTitle(): string
    {
        return 'Configuración de la Parroquia';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Configuraciones')
                ->tabs([
                    Tab::make('App')
                        ->icon('tabler-app-window')
                        ->schema([
                            TextInput::make('app.name')
                                ->label('Nombre de la aplicación')
                                ->required(),
                            FileUpload::make('app.logo')
                                ->label('Logo de la aplicación')
                                ->image()
                                ->directory('settings/logos'),
                            FileUpload::make('app.darklogo')
                                ->label('Logo oscuro de la aplicación')
                                ->image()
                                ->directory('settings/logos'),
                            FileUpload::make('app.favicon')
                                ->label('Favicon de la aplicación')
                                ->image()
                                ->directory('settings/logos'),

                        ]),
                    Tab::make('Parroquia')
                        ->icon('tabler-building-church')
                        ->schema([
                            TextInput::make('parish.name')
                                ->label('Nombre de la parroquia')
                                ->required(),
                            TextInput::make('parish.address')
                                ->label('Dirección de la parroquia')
                                ->required(),
                            TextInput::make('parish.city')
                                ->label('Ciudad de la parroquia')
                                ->required(),
                            TextInput::make('parish.zip_code')
                                ->label('Código postal de la parroquia')
                                ->required(),
                            TextInput::make('parish.phone')
                                ->label('Teléfono de la parroquia')
                                ->required(),
                            TextInput::make('parish.email')
                                ->label('Correo electrónico de la parroquia')
                                ->email()
                                ->required(),
                            TextInput::make('parish.priest')
                                ->label('Nombre del Cura')
                                ->required(),
                            TextInput::make('parish.caritas_name')
                                ->label('Nombre de Cáritas Parroquial')
                                ->required(),
                            TextInput::make('parish.caritas_director')
                                ->label('Nombre del director de Cáritas Parroquial')
                                ->required(),
                            FileUpload::make('parish.logo')
                                ->label('Logo Horizontal de la parroquia')
                                ->image()
                                ->directory('settings/logos'),
                            FileUpload::make('parish.vertical_logo')
                                ->label('Logo Vertical de la parroquia')
                                ->image()
                                ->directory('settings/logos'),
                        ]),

                    Tab::make('Otros Logos')
                        ->icon('tabler-photo')
                        ->schema([
                            FileUpload::make('caritas.logo')
                                ->label('Logo horizontal de Cáritas Diocesana')
                                ->image()
                                ->directory('settings/logos'),
                            FileUpload::make('caritas.vertical_logo')
                                ->label('Logo vertical de Cáritas Diocesana')
                                ->image()
                                ->directory('settings/logos'),
                        ]),
                ]),
        ]);
    }
}
