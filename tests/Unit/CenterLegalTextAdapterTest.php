<?php

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ITHilbert\LaravelKit\Infrastructure\Adapters\CenterLegalTextAdapter;
use ITHilbert\LaravelKit\Tests\TestCase;

class CenterLegalTextAdapterTest extends TestCase
{
    public function test_fetch_returns_content_on_successful_api_response(): void
    {
        Http::fake([
            'center.it-hilbert.com/api/impressum' => Http::response(['content' => '<h1>Impressum</h1>'], 200),
        ]);

        $adapter = new CenterLegalTextAdapter;
        $result = $adapter->fetch('impressum');

        $this->assertSame('<h1>Impressum</h1>', $result);
    }

    public function test_fetch_returns_null_on_500_response(): void
    {
        Http::fake([
            'center.it-hilbert.com/api/datenschutz' => Http::response([], 500),
        ]);

        Log::shouldReceive('warning')->once();

        $adapter = new CenterLegalTextAdapter;
        $result = $adapter->fetch('datenschutz');

        $this->assertNull($result);
    }

    public function test_fetch_returns_null_on_connection_exception(): void
    {
        Http::fake([
            'center.it-hilbert.com/api/agb' => function () {
                throw new ConnectionException('Connection refused');
            },
        ]);

        Log::shouldReceive('warning')->once();

        $adapter = new CenterLegalTextAdapter;
        $result = $adapter->fetch('agb');

        $this->assertNull($result);
    }

    public function test_fetch_returns_null_when_content_field_is_missing(): void
    {
        Http::fake([
            'center.it-hilbert.com/api/cookie-richtlinie' => Http::response(['data' => 'something'], 200),
        ]);

        Log::shouldReceive('warning')->once();

        $adapter = new CenterLegalTextAdapter;
        $result = $adapter->fetch('cookie-richtlinie');

        $this->assertNull($result);
    }

    public function test_fetch_uses_configured_base_url(): void
    {
        config()->set('laravelkit.legal_text_base_url', 'https://test.example.com');

        Http::fake([
            'test.example.com/api/impressum' => Http::response(['content' => '<p>Custom</p>'], 200),
        ]);

        $adapter = new CenterLegalTextAdapter;
        $result = $adapter->fetch('impressum');

        $this->assertSame('<p>Custom</p>', $result);
    }
}
