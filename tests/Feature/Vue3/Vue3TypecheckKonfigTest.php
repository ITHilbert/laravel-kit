<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Vue3;

use PHPUnit\Framework\TestCase;

// testebene: Konfigurations-Wächter (Arch) — liest nur Dateien des Kits, braucht keine App.
/**
 * Randfall 5 (vue3-kern-grundlage): keine `tsconfig.json` im Kit-Wurzelordner, weil der Host
 * (TypeScript 4.9, ts-loader) sie liest und an `moduleResolution: Bundler` scheitert (TS6046).
 */
final class Vue3TypecheckKonfigTest extends TestCase
{
    /** @return list<string> Fehler; leer = in Ordnung. */
    private function pruefe(string $wurzel): array
    {
        $fehler = [];

        if (is_file($wurzel.'/tsconfig.json')) {
            $fehler[] = 'tsconfig.json im Wurzelordner';
        }

        $paket = json_decode((string) file_get_contents($wurzel.'/package.json'), true);
        $befehl = (string) ($paket['scripts']['typecheck'] ?? '');

        if (! preg_match('/(?:^|\s)(?:-p|--project)\s+(\S+)/', $befehl, $treffer)) {
            return [...$fehler, 'typecheck ohne -p'];
        }

        $datei = trim($treffer[1], '"\'');

        if (basename($datei) === 'tsconfig.json') {
            $fehler[] = 'typecheck nennt eine Datei namens tsconfig.json';
        }
        if (! is_file($wurzel.'/'.$datei)) {
            return [...$fehler, 'Konfigurationsdatei fehlt: '.$datei];
        }

        $inhalt = (string) file_get_contents($wurzel.'/'.$datei);
        if (! preg_match('#"include"\s*:\s*\[[^\]]*vue3/#', $inhalt)) {
            $fehler[] = 'Konfiguration schließt vue3/ nicht ein';
        }

        return $fehler;
    }

    // AK17 / Randfall 5
    public function test_kein_tsconfig_im_wurzelordner_und_typecheck_nennt_eigene_konfiguration(): void
    {
        // Gegenprobe: der Prüfer erkennt einen fehlerhaften Aufbau.
        $platz = sys_get_temp_dir().'/kit-tsconfig-'.uniqid();
        mkdir($platz);
        file_put_contents($platz.'/tsconfig.json', '{"include":["vue3/**/*.ts"]}');
        file_put_contents($platz.'/package.json', '{"scripts":{"typecheck":"vue-tsc --noEmit"}}');
        $gegenprobe = $this->pruefe($platz);
        unlink($platz.'/tsconfig.json');
        unlink($platz.'/package.json');
        rmdir($platz);
        $this->assertNotEmpty($gegenprobe, 'Prüfer greift nicht');
        $this->assertContains('tsconfig.json im Wurzelordner', $gegenprobe);

        $this->assertSame([], $this->pruefe(dirname(__DIR__, 3)));
    }
}
