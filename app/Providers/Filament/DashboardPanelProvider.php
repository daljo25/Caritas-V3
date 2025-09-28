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
use Illuminate\Support\Facades\Schema;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $appLogo = asset('images/logo.svg');
        $appFavicon = asset('images/favicon.svg');
        $appDarkLogo = asset('images/logo-dark.svg');
        $appName = 'Parroquia';

        if (Schema::hasTable('settings')) {
            $appLogo = setting('app.logo') ?? $appLogo;
            $appFavicon = setting('app.favicon') ?? $appFavicon;
            $appDarkLogo = setting('app.darklogo') ?? $appDarkLogo;
            $appName = setting('app.name') ?? $appName;
        }

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
            ->brandLogo($appLogo)
            ->brandLogoHeight('2.5rem')
            ->favicon($appFavicon)
            ->darkModeBrandLogo($appDarkLogo)
            ->brandName($appName)
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
                    ->withSentence($appName)
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
