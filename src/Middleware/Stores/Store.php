<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Stores;

interface Store
{
    /**
     * Store the given locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function store($locale);
}
