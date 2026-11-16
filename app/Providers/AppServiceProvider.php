<?php

namespace App\Providers;

use App\Support\CachedSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Outerweb\Settings\Services\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        // The settings package binds `setting` in its own boot(), which runs
        // before this one, so replacing it here is what takes effect. The same
        // instance answers the helper and the Setting facade, which resolves
        // the class name rather than the `setting` alias.
        $this->app->singleton('setting', fn (): CachedSetting => new CachedSetting);
        $this->app->alias('setting', Setting::class);
    }
}
