<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Stores;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

final class SessionStore implements Store
{
    /**
     * Store the given locale.
     *
     * @param  string  $locale
     */
    public function store($locale): void
    {
        $key = Config::get('localized-routes.session_key');

        Session::put($key, $locale);
    }
}
