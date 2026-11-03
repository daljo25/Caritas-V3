<?php

use App\Models\User;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

it('deja entrar al panel a los usuarios en cualquier entorno', function () {
    config(['app.env' => 'production']);

    $user = new User;

    expect($user)->toBeInstanceOf(FilamentUser::class)
        ->and($user->canAccessPanel(Panel::make()))->toBeTrue();
});
