<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes;

interface ProvidesRouteParameters
{
    /**
     * Get the route parameters for this model.
     *
     * @param  string|null  $locale
     * @return array
     */
    public function getRouteParameters($locale = null);
}
