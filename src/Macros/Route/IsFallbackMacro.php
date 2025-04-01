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
     *
     * @return void
     */
    public static function register()
    {
        Route::macro('isFallback', function () {
            return App::make(RouteHelper::class)->isFallback();
        });
    }
}
