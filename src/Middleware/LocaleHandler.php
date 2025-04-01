<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Middleware;

use Illuminate\Support\Facades\App;

final class LocaleHandler
{
    /**
     * Create a new LocaleHandler instance.
     *
     * @param  \Illuminate\Support\Collection|array  $locales
     * @param  \Illuminate\Support\Collection|array  $detectors
     * @param  \Illuminate\Support\Collection|array  $stores
     * @param  \Illuminate\Support\Collection|array  $trustedDetectors
     */
    public function __construct(
        /**
         * Supported locales.
         */
        protected $locales,
        /**
         * \ToolMountain\LocalizedRoutes\Middleware\Detectors\Detector class names or instances.
         */
        protected $detectors,
        /**
         * \ToolMountain\LocalizedRoutes\Middleware\Stores\Store class names or instances.
         */
        protected $stores = [],
        /**
         * \ToolMountain\LocalizedRoutes\Middleware\Detectors\Detector class names.
         */
        protected $trustedDetectors = []
    ) {}

    /**
     * Detect any supported locale and return the first match.
     */
    public function detect(): ?string
    {
        foreach ($this->detectors as $detector) {
            $locales = (array) $this->getInstance($detector)->detect();

            foreach ($locales as $locale) {
                if ($locale && ($this->isSupportedLocale($locale) || $this->isTrustedDetector($detector))) {
                    return $locale;
                }
            }
        }

        return null;
    }

    /**
     * Store the given locale.
     */
    public function store(string $locale): void
    {
        foreach ($this->stores as $store) {
            $this->getInstance($store)->store($locale);
        }
    }

    /**
     * Check if the given locale is supported.
     */
    protected function isSupportedLocale(?string $locale): bool
    {
        return in_array($locale, $this->locales);
    }

    /**
     * Check if the given Detector class is trusted.
     *
     * @param  Detectors\Detector|string  $detector
     */
    protected function isTrustedDetector($detector): bool
    {
        if (is_string($detector)) {
            return in_array($detector, $this->trustedDetectors);
        }

        foreach ($this->trustedDetectors as $trustedDetector) {
            if ($detector instanceof $trustedDetector) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the class from Laravel's IOC container if it is a string.
     *
     * @param  mixed  $class
     * @return mixed
     */
    protected function getInstance($class)
    {
        if (is_string($class)) {
            return App::make($class);
        }

        return $class;
    }
}
