<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes;

final class LocaleConfig
{
    /**
     * The configured supported locales.
     *
     * @var array
     */
    protected $supportedLocales;

    /**
     * The configured omitted locale.
     *
     * @var string|null
     */
    protected $omittedLocale;

    /**
     * The configured fallback locale.
     *
     * @var string|null
     */
    protected $fallbackLocale;

    /**
     * The configured route action that holds a route's locale.
     *
     * @var string|null
     */
    protected $routeAction;

    /**
     * Create a new LocaleConfig instance.
     */
    public function __construct(array $config = [])
    {
        $this->supportedLocales = $config['supported_locales'] ?? [];
        $this->omittedLocale = $config['omitted_locale'] ?? null;
        $this->fallbackLocale = $config['fallback_locale'] ?? null;
        $this->routeAction = $config['route_action'] ?? null;
    }

    /**
     * Get the configured supported locales.
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * Set the supported locales.
     */
    public function setSupportedLocales(array $locales): void
    {
        $this->supportedLocales = $locales;
    }

    /**
     * Get the locale that should be omitted in the URI path.
     */
    public function getOmittedLocale(): ?string
    {
        return $this->omittedLocale;
    }

    /**
     * Set the locale that should be omitted in the URI path.
     */
    public function setOmittedLocale(?string $locale): void
    {
        $this->omittedLocale = $locale;
    }

    /**
     * Get the fallback locale.
     */
    public function getFallbackLocale(): ?string
    {
        return $this->fallbackLocale;
    }

    /**
     * Set the fallback locale.
     */
    public function setFallbackLocale(?string $locale): void
    {
        $this->fallbackLocale = $locale;
    }

    /**
     * Get the route action that holds a route's locale.
     */
    public function getRouteAction(): ?string
    {
        return $this->routeAction;
    }

    /**
     * Set the route action that holds a route's locale.
     */
    public function setRouteAction(string $action): string
    {
        return $this->routeAction = $action;
    }

    /**
     * Get the locales (not the slugs or domains).
     */
    public function getLocales(): array
    {
        $locales = $this->getSupportedLocales();

        if ($this->hasSimpleLocales()) {
            return $locales;
        }

        return array_keys($locales);
    }

    /**
     * Find the slug that belongs to the given locale.
     */
    public function findSlugByLocale(string $locale): ?string
    {
        if (! $this->isSupportedLocale($locale) || $this->hasCustomDomains()) {
            return null;
        }

        return $this->getSupportedLocales()[$locale] ?? $locale;
    }

    /**
     * Find the domain that belongs to the given locale.
     */
    public function findDomainByLocale(string $locale): ?string
    {
        if (! $this->isSupportedLocale($locale) || ! $this->hasCustomDomains()) {
            return null;
        }

        return $this->getSupportedLocales()[$locale];
    }

    /**
     * Find the locale that belongs to the given slug.
     */
    public function findLocaleBySlug(?string $slug): ?string
    {
        if ($this->hasCustomDomains()) {
            return null;
        }

        if ($this->hasSimpleLocales() && $this->isSupportedLocale($slug)) {
            return $slug;
        }

        return array_search($slug, $this->getSupportedLocales()) ?: null;
    }

    /**
     * Find the locale that belongs to the given domain.
     */
    public function findLocaleByDomain(string $domain): ?string
    {
        if (! $this->hasCustomDomains()) {
            return null;
        }

        return array_search($domain, $this->getSupportedLocales()) ?: null;
    }

    /**
     * Check if there are any locales configured.
     */
    public function hasLocales(): bool
    {
        return count($this->getSupportedLocales()) > 0;
    }

    /**
     * Check if there are only locales configured,
     * and not custom slugs or domains.
     */
    public function hasSimpleLocales(): bool
    {
        return is_numeric(key($this->getSupportedLocales()));
    }

    /**
     * Check if custom slugs are configured.
     */
    public function hasCustomSlugs(): bool
    {
        return $this->hasLocales() && ! $this->hasSimpleLocales() && ! $this->hasCustomDomains();
    }

    /**
     * Check if custom domains are configured.
     */
    public function hasCustomDomains(): bool
    {
        $firstValue = array_values($this->getSupportedLocales())[0] ?? '';
        $containsDot = str_contains((string) $firstValue, '.');

        return $containsDot;
    }

    /**
     * Check if the given locale is supported.
     */
    public function isSupportedLocale(?string $locale): bool
    {
        return in_array($locale, $this->getLocales());
    }
}
