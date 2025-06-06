<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Settings\GeneralSettings;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('')
            ->login()
            ->passwordReset()
            ->unsavedChangesAlerts()
            ->emailVerification()
            ->profile(isSimple: false)
            ->spa()
            ->globalSearch(false)
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(setting('app.logo') ?? asset('images/logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(setting('app.favicon') ?? asset('images/favicon.ico'))
            ->darkModeBrandLogo(setting('app.darklogo') ?? asset('images/logo-dark.svg'))
            ->brandName(setting('app.name') ?? 'Parroquia')
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentSettingsPlugin::make()
                    ->pages([
                        GeneralSettings::class,
                    ]),
                EasyFooterPlugin::make()
                    ->footerEnabled()
                    ->withSentence(setting('app.name'))
                    ->withGithub(showLogo: true, showUrl: true)
                    ->withLogo(
                        'https://avatars.githubusercontent.com/u/7244602',
                        'https://github.com/daljo25',
                        'Creado Por Daljo25',
                        30
                    )
                    ->withBorder(),
            ]);
    }
}
