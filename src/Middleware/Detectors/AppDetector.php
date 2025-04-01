<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Detectors;

use Illuminate\Support\Facades\App;

final class AppDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        return App::getLocale();
    }
}
