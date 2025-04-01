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
     *
     * @return void
     */
    public static function register()
    {
        Route::macro('localizedUrl', function ($locale = null, $parameters = null, $absolute = true, $keepQuery = true) {
            return App::make(LocalizedUrlGenerator::class)->generateFromRequest($locale, $parameters, $absolute, $keepQuery);
        });
    }
}
