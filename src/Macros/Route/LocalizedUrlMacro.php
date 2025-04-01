<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Macros\Route;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use ToolMountain\LocalizedRoutes\LocalizedUrlGenerator;

final class LocalizedUrlMacro
{
    /**
     * Register the macro.
     */
    public static function register(): void
    {
        Route::macro('localizedUrl', fn ($locale = null, $parameters = null, $absolute = true, $keepQuery = true) => App::make(LocalizedUrlGenerator::class)->generateFromRequest($locale, $parameters, $absolute, $keepQuery));
    }
}
