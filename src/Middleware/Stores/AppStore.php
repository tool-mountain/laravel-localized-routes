<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Stores;

use Illuminate\Support\Facades\App;

final class AppStore implements Store
{
    /**
     * Store the given locale.
     *
     * @param  string  $locale
     */
    public function store($locale): void
    {
        App::setLocale($locale);
    }
}
