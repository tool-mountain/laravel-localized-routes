<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Illuminate\Routing;

use DateInterval;
use DateTimeInterface;
use Illuminate\Routing\Redirector as BaseRedirector;

final class Redirector extends BaseRedirector
{
    /**
     * Create a new redirect response to a named route.
     *
     * @param  string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function route($route, $parameters = [], $status = 302, $headers = [], ?string $locale = null)
    {
        return $this->to($this->generator->route($route, $parameters, true, $locale), $status, $headers);
    }

    /**
     * Create a new redirect response to a signed named route.
     *
     * @param  string  $route
     * @param  mixed  $parameters
     * @param  DateTimeInterface|DateInterval|int|null  $expiration
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signedRoute($route, $parameters = [], $expiration = null, $status = 302, $headers = [], ?string $locale = null)
    {
        return $this->to($this->generator->signedRoute($route, $parameters, $expiration, true, $locale), $status, $headers);
    }

    /**
     * Create a new redirect response to a signed named route.
     *
     * @param  string  $route
     * @param  DateTimeInterface|DateInterval|int|null  $expiration
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function temporarySignedRoute($route, $expiration, $parameters = [], $status = 302, $headers = [], ?string $locale = null)
    {
        return $this->to($this->generator->temporarySignedRoute($route, $expiration, $parameters, true, $locale), $status, $headers);
    }
}
