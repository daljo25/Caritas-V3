<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Settings\GeneralSettings;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Outerweb\FilamentSettings\SettingsPlugin;

class DashboardPanelProvider extends PanelProvider
{
    /**
     * Settings are read from the database on first use and kept for the rest of
     * the request: the panel is built more than once per Livewire request and
     * Filament evaluates the branding closures several times.
     *
     * @var array<string, mixed>|null
     */
    private static ?array $settings = null;

    public function panel(Panel $panel): Panel
    {
        $appLogo = asset('images/logo.svg');
        $appFavicon = asset('images/favicon.svg');
        $appDarkLogo = asset('images/logo-dark.svg');

        // The panel is resolved before outerweb/settings registers its `setting`
        // binding, so read the settings lazily, when Filament renders them.
        $setting = fn (string $key, string $default): string => $this->setting($key, $default);

        $footer = EasyFooterPlugin::make()
            ->footerEnabled()
            ->withGithub(showLogo: true, showUrl: true)
            ->withLogo(
                'https://avatars.githubusercontent.com/u/7244602',
                'https://github.com/daljo25',
                'Creado Por Daljo25',
                30
            )
            ->withBorder();

        return $panel
            ->default()
            ->id('dashboard')
            ->path('')
            ->login()
            ->passwordReset()
            ->unsavedChangesAlerts()
            ->profile(isSimple: false)
            ->spa()
            ->globalSearch(false)
            ->maxContentWidth(Width::Full)
            ->assets([
                // El footer de filament-easy-footer usa utilidades de Tailwind
                // que Filament 5 no compila sin tema propio (ver nota del CSS).
                Css::make('footer-compat', public_path('css/filament-footer.css'))
                    ->relativePublicPath('css/filament-footer.css'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(fn (): string => $setting('app.logo', $appLogo))
            ->brandLogoHeight('2.5rem')
            ->favicon(fn (): string => $setting('app.favicon', $appFavicon))
            ->darkModeBrandLogo(fn (): string => $setting('app.darklogo', $appDarkLogo))
            ->brandName(fn (): string => $setting('app.name', 'Parroquia'))
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
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
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->bootUsing(function () use ($footer, $setting): void {
                $footer->withSentence($setting('app.name', 'Parroquia'));
            })
            ->plugins([
                SettingsPlugin::make()
                    ->pages([
                        GeneralSettings::class,
                    ]),
                $footer,
            ]);
    }

    /**
     * Read a setting lazily, loading the whole table once per request.
     */
    private function setting(string $key, string $default): string
    {
        if (self::$settings === null) {
            self::$settings = Schema::hasTable('settings')
                ? (array) app('setting')->get()
                : [];
        }

        return (string) (data_get(self::$settings, $key) ?? $default);
    }
}
