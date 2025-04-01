<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Macros\Route;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use ToolMountain\LocalizedRoutes\LocalizedRoutesRegistrar;

final class LocalizedMacro
{
    /**
     * Register the macro.
     */
    public static function register(): void
    {
        Route::macro('localized', function ($closure, $options = []) {
            App::make(LocalizedRoutesRegistrar::class)->register($closure, $options);
        });
    }
}
