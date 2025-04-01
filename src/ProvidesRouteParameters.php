<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes;

interface ProvidesRouteParameters
{
    /**
     * Get the route parameters for this model.
     */
    public function getRouteParameters(?string $locale = null): array;
}
