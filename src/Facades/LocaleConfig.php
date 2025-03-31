<?php

namespace ToolMountain\LocalizedRoutes\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * LocaleConfig Facade
 *
 * @mixin \ToolMountain\LocalizedRoutes\LocaleConfig
 */
class LocaleConfig extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'locale-config';
    }
}
