<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes;

use Closure;
use CodeZero\BrowserLocale\Laravel\BrowserLocaleServiceProvider;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Support\ServiceProvider;
use ToolMountain\LocalizedRoutes\Illuminate\Routing\Redirector;
use ToolMountain\LocalizedRoutes\Illuminate\Routing\UrlGenerator;
use ToolMountain\LocalizedRoutes\Macros\Route\HasLocalizedMacro;
use ToolMountain\LocalizedRoutes\Macros\Route\IsFallbackMacro;
use ToolMountain\LocalizedRoutes\Macros\Route\IsLocalizedMacro;
use ToolMountain\LocalizedRoutes\Macros\Route\LocalizedMacro;
use ToolMountain\LocalizedRoutes\Macros\Route\LocalizedUrlMacro;
use ToolMountain\LocalizedRoutes\Middleware\LocaleHandler;
use ToolMountain\UriTranslator\UriTranslatorServiceProvider;

final class LocalizedRoutesServiceProvider extends ServiceProvider
{
    /**
     * The package name.
     *
     * @var string
     */
    protected $name = 'localized-routes';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPublishableFiles();
        $this->registerMacros();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfig();
        $this->registerLocaleConfig();
        $this->registerLocaleHandler();
        $this->registerUrlGenerator();
        $this->registerRedirector();
        $this->registerProviders();
    }

    /**
     * Register macros.
     */
    protected function registerMacros(): void
    {
        HasLocalizedMacro::register();
        IsFallbackMacro::register();
        IsLocalizedMacro::register();
        LocalizedMacro::register();
        LocalizedUrlMacro::register();
    }

    /**
     * Register the publishable files.
     */
    protected function registerPublishableFiles(): void
    {
        $this->publishes([
            __DIR__."/../config/{$this->name}.php" => config_path("{$this->name}.php"),
        ], 'config');
    }

    /**
     * Merge published configuration file with
     * the original package configuration file.
     */
    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom(__DIR__."/../config/{$this->name}.php", $this->name);
    }

    /**
     * Registers the package dependencies
     */
    protected function registerProviders(): void
    {
        $this->app->register(BrowserLocaleServiceProvider::class);
        $this->app->register(UriTranslatorServiceProvider::class);
    }

    /**
     * Register the LocaleConfig binding.
     */
    protected function registerLocaleConfig(): void
    {
        $this->app->bind(LocaleConfig::class, fn (\Illuminate\Foundation\Application $app) => new LocaleConfig($app['config'][$this->name]));

        $this->app->bind('locale-config', LocaleConfig::class);
    }

    /**
     * Register LocaleHandler.
     */
    protected function registerLocaleHandler(): void
    {
        $this->app->bind(LocaleHandler::class, function (\Illuminate\Foundation\Application $app) {
            $locales = $app['locale-config']->getLocales();
            $detectors = $app['config']->get("{$this->name}.detectors");
            $stores = $app['config']->get("{$this->name}.stores");
            $trustedDetectors = $app['config']->get("{$this->name}.trusted_detectors");

            return new LocaleHandler($locales, $detectors, $stores, $trustedDetectors);
        });
    }

    /**
     * Register a custom URL generator that extends the one that comes with Laravel.
     * This will override a few methods that enables us to generate localized URLs.
     *
     * This method is an exact copy from:
     * \Illuminate\Routing\RoutingServiceProvider
     */
    protected function registerUrlGenerator(): void
    {
        $this->app->singleton('url', function (\Illuminate\Foundation\Application $app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            return new UrlGenerator(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                ), $app['config']['app.asset_url']
            );
        });

        $this->app->extend('url', function (UrlGeneratorContract $url, $app) {
            // Next we will set a few service resolvers on the URL generator so it can
            // get the information it needs to function. This just provides some of
            // the convenience features to this URL generator like "signed" URLs.
            $url->setSessionResolver(fn () => $this->app['session'] ?? null);

            $url->setKeyResolver(fn () => $this->app->make('config')->get('app.key'));

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function (\Illuminate\Foundation\Application $app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    /**
     * Get the URL generator request rebinder.
     *
     * This method is an exact copy from:
     * \Illuminate\Routing\RoutingServiceProvider
     *
     * @return Closure
     */
    protected function requestRebinder()
    {
        return function (\Illuminate\Foundation\Application $app, $request) {
            $app['url']->setRequest($request);
        };
    }

    /**
     * Register a custom URL redirector that extends the one that comes with Laravel.
     * This will override a few methods that enables us to redirect to localized URLs.
     *
     * This method is an exact copy from:
     * \Illuminate\Routing\RoutingServiceProvider
     */
    protected function registerRedirector(): void
    {
        $this->app->singleton('redirect', function (\Illuminate\Foundation\Application $app) {
            $redirector = new Redirector($app['url']);

            // If the session is set on the application instance, we'll inject it into
            // the redirector instance. This allows the redirect responses to allow
            // for the quite convenient "with" methods that flash to the session.
            if (isset($app['session.store'])) {
                $redirector->setSession($app['session.store']);
            }

            return $redirector;
        });
    }
}
