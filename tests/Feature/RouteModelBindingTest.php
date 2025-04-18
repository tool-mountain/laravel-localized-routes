<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use ToolMountain\LocalizedRoutes\Middleware\SetLocale;
use ToolMountain\LocalizedRoutes\Tests\Stubs\Models\ModelOneWithRouteBinding;
use ToolMountain\LocalizedRoutes\Tests\TestCase;

final class RouteModelBindingTest extends TestCase
{
    #[Test]
    public function it_loads_a_route_with_a_localized_route_key_based_on_the_active_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);

        $model = (new ModelOneWithRouteBinding([
            'slug' => [
                'en' => 'en-slug',
                'nl' => 'nl-slug',
            ],
        ]))->setKeyName('slug');

        App::instance(ModelOneWithRouteBinding::class, $model);

        Route::get('test/{model}', function (ModelOneWithRouteBinding $model) {})
            ->middleware(['web']);

        Route::localized(function () {
            Route::get('test/{model}', function (ModelOneWithRouteBinding $model) {})
                ->middleware(['web', SetLocale::class]);
        });

        $this->setAppLocale('nl');

        $this->get('test/nl-slug')->assertOk();
        $this->get('test/en-slug')->assertNotFound();

        $this->get('nl/test/nl-slug')->assertOk();
        $this->get('nl/test/en-slug')->assertNotFound();

        $this->get('en/test/en-slug')->assertOk();
        $this->get('en/test/nl-slug')->assertNotFound();
    }

    #[Test]
    public function it_loads_a_route_with_a_custom_localized_route_key_based_on_the_active_locale(): void
    {
        $this->setSupportedLocales(['en', 'nl']);

        $model = (new ModelOneWithRouteBinding([
            'slug' => [
                'en' => 'en-slug',
                'nl' => 'nl-slug',
            ],
        ]))->setKeyName('id');

        App::instance(ModelOneWithRouteBinding::class, $model);

        Route::get('test/{model:slug}', function (ModelOneWithRouteBinding $model) {})
            ->middleware(['web']);

        Route::localized(function () {
            Route::get('test/{model:slug}', function (ModelOneWithRouteBinding $model) {})
                ->middleware(['web', SetLocale::class]);
        });

        $this->setAppLocale('nl');

        $this->get('test/nl-slug')->assertOk();
        $this->get('test/en-slug')->assertNotFound();

        $this->get('nl/test/nl-slug')->assertOk();
        $this->get('nl/test/en-slug')->assertNotFound();

        $this->get('en/test/en-slug')->assertOk();
        $this->get('en/test/nl-slug')->assertNotFound();
    }

    #[Test]
    public function it_loads_a_route_with_a_localized_route_key_with_custom_slugs(): void
    {
        $this->setSupportedLocales([
            'en' => 'english',
            'nl' => 'dutch',
        ]);

        $model = (new ModelOneWithRouteBinding([
            'slug' => [
                'en' => 'en-slug',
                'nl' => 'nl-slug',
            ],
        ]))->setKeyName('slug');

        App::instance(ModelOneWithRouteBinding::class, $model);

        Route::get('test/{model}', function (ModelOneWithRouteBinding $model) {})
            ->middleware(['web']);

        Route::localized(function () {
            Route::get('test/{model}', function (ModelOneWithRouteBinding $model) {})
                ->middleware(['web', SetLocale::class]);
        });

        $this->setAppLocale('nl');

        $this->get('test/nl-slug')->assertOk();
        $this->get('test/en-slug')->assertNotFound();

        $this->get('dutch/test/nl-slug')->assertOk();
        $this->get('dutch/test/en-slug')->assertNotFound();

        $this->get('english/test/en-slug')->assertOk();
        $this->get('english/test/nl-slug')->assertNotFound();
    }
}
