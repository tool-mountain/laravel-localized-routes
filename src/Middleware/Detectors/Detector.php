<?php

namespace ToolMountain\LocalizedRoutes\Middleware\Detectors;

interface Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect();
}
