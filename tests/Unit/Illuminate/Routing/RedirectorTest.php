<?php

declare(strict_types=1);

namespace ToolMountain\LocalizedRoutes\Tests\Unit\Illuminate\Routing;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use ToolMountain\LocalizedRoutes\Tests\TestCase;

final class RedirectorTest extends TestCase
{
    #[Test]
    public function it_redirects_to_a_named_route_in_the_current_locale(): void
    {
        $this->setAppLocale('en');

        Route::get('en/target/route')->name('en.target.route');
        Route::get('redirect', fn () => Redirect::route('target.route'));

        $response = $this->get('/redirect');

        $response->assertRedirect('/en/target/route');
    }

    #[Test]
    public function it_redirects_to_a_named_route_in_a_specific_locale(): void
    {
        $this->setAppLocale('en');

        Route::get('en/target/route')->name('en.target.route');
        Route::get('nl/target/route')->name('nl.target.route');
        Route::get('redirect', fn () => Redirect::route('target.route', [], 302, [], 'nl'));

        $response = $this->get('/redirect');

        $response->assertRedirect('/nl/target/route');
    }

    #[Test]
    public function it_redirects_to_a_signed_route_in_the_current_locale(): void
    {
        $this->setAppLocale('en');

        Route::get('en/target/route')->name('en.target.route');
        Route::get('redirect', fn () => Redirect::signedRoute('target.route'));

        $response = $this->get('/redirect');

        $response->assertRedirect(URL::signedRoute('target.route'));
    }

    #[Test]
    public function it_redirects_to_a_signed_route_in_a_specific_locale(): void
    {
        $this->setAppLocale('en');

        Route::get('en/target/route')->name('en.target.route');
        Route::get('nl/target/route')->name('nl.target.route');
        Route::get('redirect', fn () => Redirect::signedRoute('target.route', [], null, 302, [], 'nl'));

        $response = $this->get('/redirect');

        $response->assertRedirect(URL::signedRoute('target.route', [], null, true, 'nl'));
    }

    #[Test]
    public function it_redirects_to_a_temporary_signed_route_in_the_current_locale(): void
    {
        $this->setAppLocale('en');

        Route::get('en/target/route')->name('en.target.route');
        Route::get('redirect', fn () => Redirect::temporarySignedRoute('target.route', now()->addMinutes(30)));

        $response = $this->get('/redirect');

        $response->assertRedirect(URL::temporarySignedRoute('target.route', now()->addMinutes(30)));
    }

    #[Test]
    public function it_redirects_to_a_temporary_signed_route_in_a_specific_locale(): void
    {
        $this->setAppLocale('en');

        Route::get('en/target/route')->name('en.target.route');
        Route::get('nl/target/route')->name('nl.target.route');
        Route::get('redirect', fn () => Redirect::temporarySignedRoute('target.route', now()->addMinutes(30), [], 302, [], 'nl'));

        $response = $this->get('/redirect');

        $response->assertRedirect(URL::temporarySignedRoute('target.route', now()->addMinutes(30), [], true, 'nl'));
    }
}
