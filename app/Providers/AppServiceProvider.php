<?php

namespace App\Providers;

use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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

        // Filament v4 makes layout components span a single grid column by
        // default. Every Fieldset in this app was written for the v3 behavior,
        // where they filled the whole width.
        Fieldset::configureUsing(fn (Fieldset $fieldset) => $fieldset->columnSpanFull());

        // Filament v4 applies table filters only when the user submits them.
        // The app was built for the v3 behavior, where changing a filter
        // reloads the table straight away.
        Table::configureUsing(fn (Table $table) => $table->deferFilters(false));
    }
}
