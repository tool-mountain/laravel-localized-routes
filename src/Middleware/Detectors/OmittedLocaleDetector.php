<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Detectors;

use Illuminate\Support\Facades\Config;

final class OmittedLocaleDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        return Config::get('localized-routes.omitted_locale') ?: null;
    }
}
