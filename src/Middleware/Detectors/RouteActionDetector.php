<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware\Detectors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

final class RouteActionDetector implements Detector
{
    /**
     * The current Route.
     *
     * @var \Illuminate\Routing\Route
     */
    protected $route;

    /**
     * Create a new RouteActionDetector instance.
     */
    public function __construct(Request $request)
    {
        $this->route = $request->route();
    }

    /**
     * Detect the locale.
     *
     * @return string|array|null
     */
    public function detect()
    {
        $action = Config::get('localized-routes.route_action');

        return $this->route->getAction($action);
    }
}
