<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware;

use Closure;

final class SetLocale
{
    /**
     * Create a new SetLocale instance.
     */
    public function __construct(
        /**
         * LocaleHandler.
         */
        protected LocaleHandler $handler
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
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
