<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use ITHilbert\LaravelKit\Mcp\Tools\BackupSeedersTool;
use ITHilbert\LaravelKit\Mcp\Tools\RemoteExecuteTool;
use ITHilbert\LaravelKit\Mcp\Tools\RemoteSyncTool;
use ITHilbert\LaravelKit\Tests\TestCase;
use Laravel\Mcp\Request;

/**
 * Bugfix #2470: Boost ruft Tools mit einem Request auf, nicht mit entpackten Argumenten.
 */
final class BoostToolsRequestTest extends TestCase
{
    private function remoteConfig(): void
    {
        config(['mcp_remote.host' => 'example.test', 'mcp_remote.user' => 'deploy', 'mcp_remote.path' => '/var/www', 'mcp_remote.port' => 22]);
    }

    private function text(object $response): string
    {
        return (string) $response->content();
    }

    // AK1
    public function test_remote_execute_fuehrt_command_aus_dem_request_aus(): void
    {
        Process::fake();
        $this->remoteConfig();

        $antwort = (new RemoteExecuteTool)->handle(new Request(['command' => 'php artisan about']));

        $this->assertStringContainsString('Erfolg', $this->text($antwort));
        Process::assertRan(fn ($process) => str_contains($process->command, 'php artisan about'));
    }

    // AK1
    public function test_remote_execute_meldet_fehlende_remote_konfiguration_als_response(): void
    {
        Process::fake();
        config(['mcp_remote.host' => null, 'mcp_remote.user' => null, 'mcp_remote.path' => null]);

        $antwort = (new RemoteExecuteTool)->handle(new Request(['command' => 'ls']));

        $this->assertStringContainsString('Konfiguration fehlt', $this->text($antwort));
        Process::assertNothingRan();
    }

    // AK2
    public function test_remote_execute_ohne_oder_mit_leerem_command_startet_keinen_prozess(): void
    {
        Process::fake();
        $this->remoteConfig();

        foreach ([[], ['command' => '']] as $argumente) {
            $antwort = (new RemoteExecuteTool)->handle(new Request($argumente));

            $this->assertStringContainsString('Fehler', $this->text($antwort));
        }
        Process::assertNothingRan();
    }

    // AK3
    public function test_remote_sync_verarbeitet_files_aus_dem_request(): void
    {
        Process::fake();
        $this->remoteConfig();

        $antwort = (new RemoteSyncTool)->handle(new Request(['files' => ['app/A.php', 'app/B.php']]));

        $this->assertStringContainsString('Erfolg', $this->text($antwort));
        Process::assertRan(fn ($process) => str_contains($process->command, 'app/A.php') && str_contains($process->command, 'app/B.php'));
    }

    // AK3
    public function test_remote_sync_ohne_files_oder_mit_leerem_array_meldet_mindestens_eine_datei(): void
    {
        Process::fake();
        $this->remoteConfig();

        foreach ([[], ['files' => []]] as $argumente) {
            $antwort = (new RemoteSyncTool)->handle(new Request($argumente));

            $this->assertStringContainsString('Mindestens eine Datei', $this->text($antwort));
        }
        Process::assertNothingRan();
    }

    // AK4
    public function test_backup_seeders_tool_nimmt_einen_request_im_boost_aufrufstil(): void
    {
        Artisan::command('db:backup-seeders', function (): int {
            $this->line('fake-backup');

            return 0;
        });

        $antwort = (new BackupSeedersTool)->handle(new Request([]));

        $this->assertStringContainsString('Backup completed', $this->text($antwort));
    }
}
