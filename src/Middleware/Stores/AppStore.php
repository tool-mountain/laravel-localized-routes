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
     * @return void
     */
    public function store($locale)
    {
        App::setLocale($locale);
    }
}
