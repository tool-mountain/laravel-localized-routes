<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Stores;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

final class CookieStore implements Store
{
    /**
     * Store the given locale.
     *
     * @param  string  $locale
     */
    public function store($locale): void
    {
        $name = Config::get('localized-routes.cookie_name');
        $minutes = Config::get('localized-routes.cookie_minutes');

        Cookie::queue($name, $locale, $minutes);
    }
}
