<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;
use ITHilbert\LaravelKit\Infrastructure\Adapters\CenterLegalTextAdapter;
use ITHilbert\LaravelKit\LaravelKitServiceProvider;
use ITHilbert\LaravelKit\Mcp\Tools\BackupSeedersTool;
use ITHilbert\LaravelKit\Mcp\Tools\RemoteExecuteTool;
use ITHilbert\LaravelKit\Mcp\Tools\RemoteSyncTool;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Charakterisierung des Boot-Verhaltens (Ist-Stand, keine Spec).
 */
final class BootTest extends TestCase
{
    // Charakterisierung: Provider
    public function test_provider_ist_registriert(): void
    {
        $this->assertArrayHasKey(LaravelKitServiceProvider::class, $this->app->getLoadedProviders());
    }

    // Charakterisierung: Config
    public function test_config_schluessel_sind_zusammengefuehrt(): void
    {
        $this->assertSame('LaravelKit', config('laravelkit.name'));
        $this->assertSame('https://center.it-hilbert.com', config('laravelkit.legal_text_base_url'));
        $this->assertSame(5, config('laravelkit.legal_text_timeout'));
        $this->assertIsArray(config('laravelkit.backup_seeders.ignore_tables'));
        $this->assertContains('jobs', config('laravelkit.backup_seeders.ignore_tables'));
        $this->assertIsArray(config('mcp_remote'));
    }

    // Charakterisierung: Container
    public function test_legal_text_port_ist_an_center_adapter_gebunden(): void
    {
        $this->assertInstanceOf(CenterLegalTextAdapter::class, $this->app->make(LegalTextPort::class));
    }

    // Charakterisierung: MCP-Tools werden in die Boost-Config eingetragen
    public function test_mcp_tools_werden_in_boost_config_eingetragen(): void
    {
        $tools = config('boost.mcp.tools.include');

        $this->assertContains(BackupSeedersTool::class, $tools);
        $this->assertContains(RemoteExecuteTool::class, $tools);
        $this->assertContains(RemoteSyncTool::class, $tools);
    }

    // Charakterisierung: Artisan-Befehle
    public function test_artisan_befehle_sind_registriert(): void
    {
        $befehle = array_keys($this->app->make(Kernel::class)->all());

        foreach ([
            'audit:run', 'audit:fetch', 'audit:feedback', 'changelog:show', 'db:backup-seeders',
            'laravelkit:paths', 'laravelkit:copyfiles', 'install:all', 'install:customer',
            'install:DataTables', 'install:Debugbar', 'install:iSeed', 'install:userauth',
            'install:mix', 'ftp:open', 'ssh:open',
        ] as $befehl) {
            $this->assertContains($befehl, $befehle, "Befehl {$befehl} fehlt");
        }
    }

    // Charakterisierung: Migration
    public function test_migration_legt_log_tabelle_an(): void
    {
        $this->artisan('migrate')->assertSuccessful();

        $this->assertTrue(Schema::hasTable('log'));
        $this->assertTrue(Schema::hasColumns('log', ['id', 'log', 'created_at', 'updated_at']));
    }

    // Charakterisierung: Blade-Komponenten
    public function test_blade_komponenten_sind_registriert(): void
    {
        $aliase = Blade::getClassComponentAliases();

        foreach (['laravelkit-impressum', 'laravelkit-datenschutz', 'laravelkit-agb', 'laravelkit-cookies'] as $alias) {
            $this->assertArrayHasKey($alias, $aliase);
        }
    }

    // Charakterisierung: View-Namespace
    public function test_view_namespace_laravelkit_ist_registriert(): void
    {
        $this->assertTrue(view()->exists('laravelkit::components.impressum'));
        $this->assertTrue(view()->exists('laravelkit::components.cookie-richtlinie'));
    }

    // Charakterisierung: Routen — die Web-Routendatei registriert aktuell nichts
    public function test_provider_registriert_keine_routen(): void
    {
        $uris = array_map(
            static fn ($route): string => $route->uri(),
            Route::getRoutes()->getRoutes()
        );

        $this->assertSame([], array_values(array_filter($uris, static fn (string $uri): bool => str_contains($uri, 'laravelkit'))));
    }
}
