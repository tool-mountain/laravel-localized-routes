<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Macros\Route;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use ToolMountain\LocalizedRoutes\RouteHelper;

final class IsFallbackMacro
{
    /**
     * Register the macro.
     */
    public static function register(): void
    {
        Route::macro('isFallback', fn () => App::make(RouteHelper::class)->isFallback());
    }
}
