<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Detectors;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

final class UserDetector implements Detector
{
    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $user = Auth::user();

        if ($user === null) {
            return null;
        }

        $attribute = Config::get('localized-routes.user_attribute');

        return $user->getAttributeValue($attribute);
    }
}
