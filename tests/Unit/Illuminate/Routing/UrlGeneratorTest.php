<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Tests\Unit\Illuminate\Routing;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use ToolMountain\LocalizedRoutes\Illuminate\Routing\UrlGenerator;
use ToolMountain\LocalizedRoutes\Tests\Stubs\Controller;
use ToolMountain\LocalizedRoutes\Tests\Stubs\Models\ModelOneWithRouteBinding;
use ToolMountain\LocalizedRoutes\Tests\TestCase;

final class UrlGeneratorTest extends TestCase
{
    #[Test]
    public function it_binds_our_custom_url_generator_class(): void
    {
        $this->assertInstanceOf(UrlGenerator::class, App::make('url'));
        $this->assertInstanceOf(UrlGenerator::class, App::make('redirect')->getUrlGenerator());
    }

    #[Test]
    public function it_gets_the_url_of_a_named_route_as_usual(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('weirdly-named-route')->name('en');
        Route::get('route')->name('route');
        Route::get('en/route')->name('en.route');
        Route::get('nl/route')->name('nl.route');
        Route::get('route/name')->name('route.name');
        Route::get('en/route/name')->name('en.route.name');
        Route::get('nl/route/name')->name('nl.route.name');

        $this->assertEquals(URL::to('weirdly-named-route'), URL::route('en'));
        $this->assertEquals(URL::to('route'), URL::route('route'));
        $this->assertEquals(URL::to('en/route'), URL::route('en.route'));
        $this->assertEquals(URL::to('nl/route'), URL::route('nl.route'));
        $this->assertEquals(URL::to('route/name'), URL::route('route.name'));
        $this->assertEquals(URL::to('en/route/name'), URL::route('en.route.name'));
        $this->assertEquals(URL::to('nl/route/name'), URL::route('nl.route.name'));
    }

    #[Test]
    public function it_gets_the_url_of_a_route_in_the_current_locale_if_the_given_route_name_does_not_exist(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('en/route')->name('en.route.name');

        $this->assertEquals(URL::to('en/route'), URL::route('route.name'));
    }

    #[Test]
    public function it_throws_if_no_valid_route_can_be_found(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('wrong-route')->name('wrong-route');

        $this->expectException(InvalidArgumentException::class);

        URL::route('route');
    }

    #[Test]
    public function it_throws_if_no_valid_localized_route_can_be_found(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('nl/route')->name('nl.route.name');

        $this->expectException(InvalidArgumentException::class);

        URL::route('route.name');
    }

    #[Test]
    public function it_gets_the_url_of_a_route_in_the_given_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('en/route')->name('en.route.name');
        Route::get('nl/route')->name('nl.route.name');

        $this->assertEquals(URL::to('nl/route'), URL::route('route.name', locale: 'nl'));
        $this->assertEquals(URL::to('nl/route'), URL::route('en.route.name', locale: 'nl'));
        $this->assertEquals(URL::to('nl/route'), URL::route('nl.route.name', locale: 'nl'));
    }

    #[Test]
    public function it_gets_the_url_of_a_route_in_the_given_locale_when_using_custom_domains(): void
    {
        $this->setSupportedLocales([
            'en' => 'en.domain.test',
            'nl' => 'nl.domain.test',
        ]);
        $this->setAppLocale('en');

        Route::get('route')->name('en.route.name')->domain('en.domain.test');
        Route::get('route')->name('nl.route.name')->domain('nl.domain.test');

        $this->assertEquals('http://nl.domain.test/route', URL::route('route.name', locale: 'nl'));
        $this->assertEquals('http://nl.domain.test/route', URL::route('en.route.name', locale: 'nl'));
        $this->assertEquals('http://nl.domain.test/route', URL::route('nl.route.name', locale: 'nl'));
    }

    #[Test]
    public function it_gets_the_url_of_a_route_in_the_given_locale_when_using_custom_slugs(): void
    {
        $this->setSupportedLocales([
            'en' => 'english',
            'nl' => 'dutch',
        ]);

        Route::get('english/route')->name('en.route.name');
        Route::get('dutch/route')->name('nl.route.name');

        $this->assertEquals(URL::to('dutch/route'), URL::route('route.name', locale: 'nl'));
        $this->assertEquals(URL::to('dutch/route'), URL::route('en.route.name', locale: 'nl'));
        $this->assertEquals(URL::to('dutch/route'), URL::route('nl.route.name', locale: 'nl'));
    }

    #[Test]
    public function it_always_gets_the_url_of_a_localized_route_if_a_locale_is_specified(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('route')->name('route.name');
        Route::get('nl/route')->name('nl.route.name');

        $this->assertEquals(URL::to('nl/route'), URL::route('route.name', locale: 'nl'));
    }

    #[Test]
    public function it_returns_a_registered_non_localized_url_if_a_localized_version_does_not_exist(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('route')->name('route.name');
        Route::get('nl/route')->name('nl.route.name');

        $this->assertEquals(URL::to('route'), URL::route('route.name', locale: 'en'));
    }

    #[Test]
    public function it_throws_if_no_valid_route_can_be_found_for_the_given_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('en/route')->name('en.route.name');

        $this->expectException(RouteNotFoundException::class);

        URL::route('en.route.name', locale: 'nl');
    }

    #[Test]
    public function it_uses_a_fallback_locale_when_the_requested_locale_is_unsupported(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');
        $this->setFallbackLocale('en');

        Route::get('en/route')->name('en.route');
        Route::get('nl/route')->name('nl.route');

        $this->assertEquals(URL::to('en/route'), URL::route('route', locale: 'en'));
        $this->assertEquals(URL::to('nl/route'), URL::route('route', locale: 'nl'));
        $this->assertEquals(URL::to('en/route'), URL::route('route', locale: 'fr'));
    }

    #[Test]
    public function it_uses_a_fallback_locale_when_the_requested_locale_is_not_registered(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');
        $this->setFallbackLocale('en');

        Route::get('en/route')->name('en.route');

        $this->assertEquals(URL::to('en/route'), URL::route('route', locale: 'en'));
        $this->assertEquals(URL::to('en/route'), URL::route('route', locale: 'nl'));
    }

    #[Test]
    public function it_throws_if_you_do_not_specify_a_name_for_a_localized_route(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        Route::get('en/route')->name('en.');

        $this->expectException(RouteNotFoundException::class);

        URL::route('en.', locale: 'en');
    }

    #[Test]
    public function it_generates_a_url_for_a_route_with_a_default_localized_route_key(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $model = (new ModelOneWithRouteBinding([
            'slug' => [
                'en' => 'en-slug',
                'nl' => 'nl-slug',
            ],
        ]))->setKeyName('slug');

        App::instance(ModelOneWithRouteBinding::class, $model);

        Route::get('en/route/{slug}')->name('en.route.name');
        Route::get('nl/route/{slug}')->name('nl.route.name');

        $this->assertEquals(URL::to('en/route/en-slug'), URL::route('route.name', [$model]));
        $this->assertEquals(URL::to('en/route/en-slug'), URL::route('route.name', [$model], locale: 'en'));
        $this->assertEquals(URL::to('nl/route/nl-slug'), URL::route('route.name', [$model], locale: 'nl'));
    }

    #[Test]
    public function it_generates_a_url_for_a_route_with_a_custom_localized_route_key(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $model = (new ModelOneWithRouteBinding([
            'slug' => [
                'en' => 'en-slug',
                'nl' => 'nl-slug',
            ],
        ]))->setKeyName('id');

        App::instance(ModelOneWithRouteBinding::class, $model);

        Route::get('en/route/{model:slug}')->name('en.route.name');
        Route::get('nl/route/{model:slug}')->name('nl.route.name');

        $this->assertEquals(URL::to('en/route/en-slug'), URL::route('route.name', [$model]));
        $this->assertEquals(URL::to('en/route/en-slug'), URL::route('route.name', [$model], locale: 'en'));
        $this->assertEquals(URL::to('nl/route/nl-slug'), URL::route('route.name', [$model], locale: 'nl'));
    }

    #[Test]
    public function it_generates_a_signed_route_url_for_the_current_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $callback = fn () => Request::hasValidSignature()
            ? 'Valid Signature'
            : 'Invalid Signature';

        Route::get('en/route', $callback)->name('en.route.name');
        Route::get('en/other/route', $callback)->name('en.other.route.name');

        $validUrl = URL::signedRoute('route.name');
        $tamperedUrl = str_replace('en/route', 'en/other/route', $validUrl);

        $this->get($validUrl)->assertSee('Valid Signature');
        $this->get($tamperedUrl)->assertSee('Invalid Signature');
    }

    #[Test]
    public function it_generates_a_signed_route_url_for_a_specific_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $callback = fn () => Request::hasValidSignature()
            ? 'Valid Signature'
            : 'Invalid Signature';

        Route::get('en/route', $callback)->name('en.route.name');
        Route::get('nl/route', $callback)->name('nl.route.name');

        $validUrl = URL::signedRoute('route.name', locale: 'nl');
        $tamperedUrl = str_replace('nl/route', 'en/route', $validUrl);

        $this->get($validUrl)->assertSee('Valid Signature');
        $this->get($tamperedUrl)->assertSee('Invalid Signature');
    }

    #[Test]
    public function it_generates_a_temporary_signed_route_url_for_the_current_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $callback = fn () => Request::hasValidSignature()
            ? 'Valid Signature'
            : 'Expired Signature';

        Route::get('en/route', $callback)->name('en.route.name');

        $validUrl = URL::temporarySignedRoute('route.name', now()->addHour());
        $expiredUrl = URL::temporarySignedRoute('route.name', now()->subHour());

        $this->get($validUrl)->assertSee('Valid Signature');
        $this->get($expiredUrl)->assertSee('Expired Signature');
    }

    #[Test]
    public function it_generates_a_temporary_signed_route_url_for_a_specific_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);
        $this->setAppLocale('en');

        $callback = fn () => Request::hasValidSignature()
            ? 'Valid Signature'
            : 'Expired Signature';

        Route::get('en/route', $callback)->name('en.route.name');
        Route::get('nl/route', $callback)->name('nl.route.name');

        $validUrl = URL::temporarySignedRoute('route.name', now()->addHour(), locale: 'nl');
        $expiredUrl = URL::temporarySignedRoute('route.name', now()->subHour(), locale: 'nl');

        $this->get($validUrl)->assertSee('Valid Signature');
        $this->get($expiredUrl)->assertSee('Expired Signature');
    }

    #[Test]
    public function it_throws_a_route_not_found_exception_for_missing_route_names_when_generating_a_route_url(): void
    {
        $this->expectException(RouteNotFoundException::class);

        URL::route('missing.route');
    }

    #[Test]
    public function the_app_locale_is_correctly_restored_when_catching_a_route_not_found_exception_when_generating_a_route_url(): void
    {
        $this->setAppLocale('en');

        try {
            URL::route('missing.route', locale: 'nl');
        } catch (RouteNotFoundException) {
        }

        $this->assertEquals('en', App::getLocale());
    }

    #[Test]
    public function it_throws_a_route_not_found_exception_for_missing_route_names_when_generating_a_signed_route_url(): void
    {
        $this->expectException(RouteNotFoundException::class);

        URL::signedRoute('missing.route');
    }

    #[Test]
    public function the_app_locale_is_correctly_restored_when_catching_a_route_not_found_exception_when_generating_a_signed_route_url(): void
    {
        $this->setAppLocale('en');

        try {
            URL::signedRoute('missing.route', locale: 'nl');
        } catch (RouteNotFoundException) {
        }

        $this->assertEquals('en', App::getLocale());
    }

    #[Test]
    public function it_throws_a_route_not_found_exception_for_missing_route_names_when_generating_a_temporary_signed_route_url(): void
    {
        $this->expectException(RouteNotFoundException::class);

        URL::temporarySignedRoute('missing.route', now()->addMinutes(30));
    }

    #[Test]
    public function the_app_locale_is_correctly_restored_when_catching_a_route_not_found_exception_when_generating_a_temporary_signed_route_url(): void
    {
        $this->setAppLocale('en');

        try {
            URL::temporarySignedRoute('missing.route', now()->addMinutes(30), locale: 'nl');
        } catch (RouteNotFoundException) {
        }

        $this->assertEquals('en', App::getLocale());
    }

    #[Test]
    public function it_allows_routes_to_be_cached(): void
    {
        $this->withoutExceptionHandling();
        $this->setSupportedLocales(['en']);
        $this->setAppLocale('en');

        Route::get('en/route', [Controller::class, 'index']);

        $this->cacheRoutes();

        $this->get('en/route')->assertSuccessful();
    }

    /**
     * Cache registered routes.
     */
    protected function cacheRoutes(): void
    {
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $route->prepareForSerialization();
        }

        $this->app['router']->setCompiledRoutes(
            $routes->compile()
        );
    }
}
