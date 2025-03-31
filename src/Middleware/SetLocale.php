<?php

namespace LaravelToolbox\LocalizedRoutes\Middleware;

use Closure;

class SetLocale
{
    /**
     * LocaleHandler.
     *
     * @var \LaravelToolbox\LocalizedRoutes\Middleware\LocaleHandler
     */
    protected $handler;

    /**
     * Create a new SetLocale instance.
     *
     * @param \LaravelToolbox\LocalizedRoutes\Middleware\LocaleHandler $handler
     */
    public function __construct(LocaleHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $this->handler->detect();

        if ($locale) {
            $this->handler->store($locale);
        }

        return $next($request);
    }
}
