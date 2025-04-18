<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Detectors;

use CodeZero\BrowserLocale\BrowserLocale;
use CodeZero\BrowserLocale\Filters\CombinedFilter;
use Illuminate\Support\Facades\App;

final class BrowserDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        return App::make(BrowserLocale::class)->filter(new CombinedFilter());
    }
}
