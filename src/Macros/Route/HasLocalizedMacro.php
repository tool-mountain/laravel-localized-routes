<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Macros\Route;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use ToolMountain\LocalizedRoutes\RouteHelper;

final class HasLocalizedMacro
{
    /**
     * Register the macro.
     */
    public static function register(): void
    {
        Route::macro('hasLocalized', fn (string $name, ?string $locale = null) => App::make(RouteHelper::class)->hasLocalized($name, $locale));
    }
}
