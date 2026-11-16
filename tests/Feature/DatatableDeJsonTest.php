<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use Illuminate\Support\ServiceProvider;
use ITHilbert\LaravelKit\LaravelKitServiceProvider;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * DataTable_DE.json wird wieder mit dem Paket ausgeliefert (userauth 3 lädt sie aus vendor/laravelkit).
 */
final class DatatableDeJsonTest extends TestCase
{
    private const QUELLE = 'src/Publish/Public/laravelkit/DataTable_DE.json';

    // AK1
    public function test_datei_existiert_und_ist_gueltiges_json_mit_sempty_table(): void
    {
        $pfad = dirname(__DIR__, 2).'/'.self::QUELLE;

        $this->assertFileExists($pfad);

        $daten = json_decode((string) file_get_contents($pfad), true);

        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertIsArray($daten);
        $this->assertSame('Keine Daten in der Tabelle vorhanden', $daten['sEmptyTable'] ?? null);
    }

    // AK2
    public function test_publish_tag_laravelkit_assets_liefert_die_datei_nach_public_vendor_laravelkit(): void
    {
        $pfade = ServiceProvider::pathsToPublish(LaravelKitServiceProvider::class, 'laravelkit-assets');

        $quelle = dirname(__DIR__, 2).'/'.self::QUELLE;
        $verzeichnis = dirname($quelle);
        $ziel = public_path('vendor/laravelkit');

        $treffer = null;
        foreach ($pfade as $von => $nach) {
            if (realpath($von) === realpath($quelle) || realpath($von) === realpath($verzeichnis)) {
                $treffer = $nach;
            }
        }

        $this->assertNotEmpty($pfade, 'Tag laravelkit-assets ist nicht registriert');
        $this->assertNotNull($treffer, 'Quelle DataTable_DE.json (bzw. Verzeichnis laravelkit) fehlt im Tag');
        $this->assertSame($ziel, $treffer);
    }

    // AK3
    public function test_readme_nennt_publish_tag_samt_befehl(): void
    {
        $readme = (string) file_get_contents(dirname(__DIR__, 2).'/README.md');

        $this->assertStringContainsString('laravelkit-assets', $readme);
        $this->assertStringContainsString('php artisan vendor:publish --tag=laravelkit-assets', $readme);
    }
}
