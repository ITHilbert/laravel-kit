<?php

namespace ITHilbert\LaravelKit\Tests\Feature;

use ITHilbert\LaravelKit\Components\AGB;
use ITHilbert\LaravelKit\Components\CookieRichtlinie;
use ITHilbert\LaravelKit\Components\Datenschutz;
use ITHilbert\LaravelKit\Components\Impressum;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;
use Mockery;
use Tests\TestCase;

class LegalTextComponentsTest extends TestCase
{
    public function test_impressum_component_renders_content_when_api_available(): void
    {
        $port = Mockery::mock(LegalTextPort::class);
        $port->shouldReceive('fetch')->with('impressum')->andReturn('<h1>Impressum</h1>');

        $this->app->instance(LegalTextPort::class, $port);

        $component = $this->app->make(Impressum::class);

        $this->assertSame('<h1>Impressum</h1>', $component->inhalt);
    }

    public function test_impressum_component_has_null_inhalt_when_api_unavailable(): void
    {
        $port = Mockery::mock(LegalTextPort::class);
        $port->shouldReceive('fetch')->with('impressum')->andReturn(null);

        $this->app->instance(LegalTextPort::class, $port);

        $component = $this->app->make(Impressum::class);

        $this->assertNull($component->inhalt);
    }

    public function test_datenschutz_component_fetches_correct_type(): void
    {
        $port = Mockery::mock(LegalTextPort::class);
        $port->shouldReceive('fetch')->with('datenschutz')->once()->andReturn('<p>Datenschutz</p>');

        $this->app->instance(LegalTextPort::class, $port);

        $component = $this->app->make(Datenschutz::class);

        $this->assertSame('<p>Datenschutz</p>', $component->inhalt);
    }

    public function test_agb_component_fetches_correct_type(): void
    {
        $port = Mockery::mock(LegalTextPort::class);
        $port->shouldReceive('fetch')->with('agb')->once()->andReturn('<p>AGB</p>');

        $this->app->instance(LegalTextPort::class, $port);

        $component = $this->app->make(AGB::class);

        $this->assertSame('<p>AGB</p>', $component->inhalt);
    }

    public function test_cookie_richtlinie_component_fetches_correct_type(): void
    {
        $port = Mockery::mock(LegalTextPort::class);
        $port->shouldReceive('fetch')->with('cookie-richtlinie')->once()->andReturn('<p>Cookies</p>');

        $this->app->instance(LegalTextPort::class, $port);

        $component = $this->app->make(CookieRichtlinie::class);

        $this->assertSame('<p>Cookies</p>', $component->inhalt);
    }
}
