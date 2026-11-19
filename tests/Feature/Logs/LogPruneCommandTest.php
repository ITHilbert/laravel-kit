<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Logs;

use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ITHilbert\LaravelKit\Tests\TestCase;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * log-taeglich (#1716): Command `log:prune` löscht .log-Dateien in storage/logs, die älter als N Tage sind.
 * Der Test legt storage in ein eigenes Temp-Verzeichnis; das echte storage/logs bleibt unberührt.
 * Hinweis an die Umsetzung: das Alter wird gegen `now()` (Carbon) gerechnet, damit die Grenze (Randfall 4) prüfbar ist.
 */
final class LogPruneCommandTest extends TestCase
{
    private ?string $basis = null;

    protected function tearDown(): void
    {
        $basis = $this->basis;
        parent::tearDown();
        Carbon::setTestNow();

        if ($basis !== null && is_dir($basis)) {
            // nur das eigene Temp-Verzeichnis: Schreibrechte zurückgeben, dann entfernen
            exec('chmod -R u+rwx '.escapeshellarg($basis));
            File::deleteDirectory($basis);
        }
    }

    private function basis(): string
    {
        if ($this->basis === null) {
            $this->basis = sys_get_temp_dir().'/kit-log-prune-'.bin2hex(random_bytes(6));
            mkdir($this->basis.'/storage', 0777, true);
        }

        return $this->basis;
    }

    private function logs(): string
    {
        return $this->basis().'/storage/logs';
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);
        $app->useStoragePath($this->basis().'/storage');
    }

    /**
     * @param  Application  $app
     */
    protected function tage30($app): void
    {
        $app['config']->set('laravelkit.logs', ['taeglich' => true, 'tage' => 30, 'zeitplan' => false]);
    }

    /**
     * @param  Application  $app
     */
    protected function tage35($app): void
    {
        $app['config']->set('laravelkit.logs', ['taeglich' => true, 'tage' => 35, 'zeitplan' => false]);
    }

    /**
     * @param  Application  $app
     */
    protected function tageLeer($app): void
    {
        $app['config']->set('laravelkit.logs', ['taeglich' => true, 'tage' => '', 'zeitplan' => false]);
    }

    /** Legt eine Datei unter storage/logs an; Änderungszeit = jetzt minus Sekunden. */
    private function datei(string $relativ, int $sekundenAlt, string $inhalt = 'x'): string
    {
        $pfad = $this->logs().'/'.$relativ;
        if (! is_dir(dirname($pfad))) {
            mkdir(dirname($pfad), 0777, true);
        }
        file_put_contents($pfad, $inhalt);
        touch($pfad, Carbon::now()->getTimestamp() - $sekundenAlt);

        return $pfad;
    }

    private function tage(int $tage): int
    {
        return $tage * 86400;
    }

    /** @return array{0: int, 1: string} */
    private function prune(array $optionen = []): array
    {
        $exit = Artisan::call('log:prune', $optionen);

        return [$exit, Artisan::output()];
    }

    /** Dateien aus AK8: a.log (31 Tage), b.log (29 Tage), unter/c.log (40 Tage). */
    private function dateienAusAk8(): void
    {
        $this->datei('a.log', $this->tage(31));
        $this->datei('b.log', $this->tage(29));
        $this->datei('unter/c.log', $this->tage(40));
    }

    // AK8
    public function test_loescht_nur_dateien_aelter_als_die_tage(): void
    {
        $this->dateienAusAk8();

        [$exit, $ausgabe] = $this->prune(['--days' => 30]);

        $this->assertSame(0, $exit);
        $this->assertFileDoesNotExist($this->logs().'/a.log');
        $this->assertFileDoesNotExist($this->logs().'/unter/c.log');
        $this->assertFileExists($this->logs().'/b.log');
        $this->assertStringContainsString('a.log', $ausgabe);
        $this->assertStringContainsString('unter/c.log', $ausgabe);
        $this->assertStringContainsString('2 Dateien gelöscht', $ausgabe);
    }

    // AK9
    #[DefineEnvironment('tage30')]
    public function test_ohne_days_gilt_die_config_30(): void
    {
        $this->dateienAusAk8();

        [$exit, $ausgabe] = $this->prune();

        $this->assertSame(0, $exit);
        $this->assertFileDoesNotExist($this->logs().'/a.log');
        $this->assertFileDoesNotExist($this->logs().'/unter/c.log');
        $this->assertFileExists($this->logs().'/b.log');
        $this->assertStringContainsString('2 Dateien gelöscht', $ausgabe);
    }

    // AK9
    #[DefineEnvironment('tage35')]
    public function test_ohne_days_gilt_die_config_35(): void
    {
        $this->dateienAusAk8();

        [$exit] = $this->prune();

        $this->assertSame(0, $exit);
        $this->assertFileExists($this->logs().'/a.log');
        $this->assertFileExists($this->logs().'/b.log');
        $this->assertFileDoesNotExist($this->logs().'/unter/c.log');
    }

    // AK9 (leer gesetzt: nie alles löschen; Annahme Standard 30 oder Fehler, jedenfalls bleibt, was jünger als 30 Tage ist)
    #[DefineEnvironment('tageLeer')]
    public function test_leere_config_tage_loescht_nichts_unter_30_tagen(): void
    {
        $this->dateienAusAk8();
        $this->datei('frisch.log', $this->tage(1));

        [$exit] = $this->prune();

        $this->assertSame(0, $exit);
        $this->assertFileDoesNotExist($this->logs().'/a.log');
        $this->assertFileExists($this->logs().'/b.log');
        $this->assertFileExists($this->logs().'/frisch.log');
    }

    // Randfall 9
    #[DataProvider('schlusszeilen')]
    public function test_schlusszeile_richtet_sich_nach_der_anzahl(int $anzahl, bool $trocken, string $erwartet, string $falsch): void
    {
        for ($i = 1; $i <= $anzahl; $i++) {
            $this->datei("alt{$i}.log", $this->tage(40));
        }
        $this->datei('jung.log', $this->tage(1));

        [$exit, $ausgabe] = $this->prune(['--days' => 30] + ($trocken ? ['--dry-run' => true] : []));

        $this->assertSame(0, $exit);
        $this->assertStringContainsString($erwartet, $ausgabe);
        $this->assertStringNotContainsString($falsch, $ausgabe);
        $this->assertStringContainsString(($trocken ? 'würde gelöscht: ' : 'gelöscht: ').'alt1.log', $ausgabe);
    }

    /**
     * @return array<string, array{int, bool, string, string}>
     */
    public static function schlusszeilen(): array
    {
        return [
            '1 Datei' => [1, false, '1 Datei gelöscht', '1 Dateien'],
            '2 Dateien' => [2, false, '2 Dateien gelöscht', '2 Datei gelöscht'],
            '1 Datei, dry-run' => [1, true, '1 Datei würde gelöscht', '1 Dateien'],
            '2 Dateien, dry-run' => [2, true, '2 Dateien würden gelöscht', 'Dateien würde gelöscht'],
        ];
    }

    // AK10
    public function test_dry_run_nennt_dateien_und_loescht_nichts(): void
    {
        $this->dateienAusAk8();

        [$exit, $ausgabe] = $this->prune(['--days' => 30, '--dry-run' => true]);

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('würde gelöscht', $ausgabe);
        $this->assertStringContainsString('a.log', $ausgabe);
        $this->assertStringContainsString('unter/c.log', $ausgabe);
        $this->assertFileExists($this->logs().'/a.log');
        $this->assertFileExists($this->logs().'/b.log');
        $this->assertFileExists($this->logs().'/unter/c.log');

        // Gegenprobe: derselbe Aufbau löscht ohne --dry-run
        $this->prune(['--days' => 30]);
        $this->assertFileDoesNotExist($this->logs().'/a.log');
    }

    // AK11
    /**
     * @param  int|string  $ungueltig
     */
    #[DataProvider('ungueltigeTage')]
    public function test_ungueltige_days_melden_fehler_und_loeschen_nichts(mixed $ungueltig): void
    {
        $this->dateienAusAk8();

        [$exit, $ausgabe] = $this->prune(['--days' => $ungueltig]);

        $this->assertNotSame(0, $exit);
        $this->assertStringContainsString('--days', $ausgabe);
        $this->assertFileExists($this->logs().'/a.log');
        $this->assertFileExists($this->logs().'/b.log');
        $this->assertFileExists($this->logs().'/unter/c.log');

        // Gegenprobe: mit gültigem Wert löscht derselbe Aufbau
        [$exitGueltig] = $this->prune(['--days' => 30]);
        $this->assertSame(0, $exitGueltig);
        $this->assertFileDoesNotExist($this->logs().'/a.log');
    }

    /**
     * @return array<string, array{int|string}>
     */
    public static function ungueltigeTage(): array
    {
        return ['null' => ['0'], 'negativ' => ['-5'], 'Text' => ['abc']];
    }

    // AK12
    public function test_loescht_nur_dateien_mit_endung_log(): void
    {
        $alt = $this->tage(60);
        $this->datei('.gitignore', $alt, "*\n!.gitignore\n");
        $this->datei('claude-gate.jsonl', $alt);
        $this->datei('claude-abweichungen-offen', $alt);
        $this->datei('x.log.gz', $alt);
        $this->datei('weg.log', $alt);

        [$exit] = $this->prune(['--days' => 30]);

        $this->assertSame(0, $exit);
        // Gegenprobe: das Aufräumen lief
        $this->assertFileDoesNotExist($this->logs().'/weg.log');
        foreach (['.gitignore', 'claude-gate.jsonl', 'claude-abweichungen-offen', 'x.log.gz'] as $bleibt) {
            $this->assertFileExists($this->logs().'/'.$bleibt, $bleibt);
        }
    }

    // Randfall 1
    public function test_fehlendes_logs_verzeichnis_meldet_nichts_zu_tun(): void
    {
        $this->assertDirectoryDoesNotExist($this->logs());

        [$exit, $ausgabe] = $this->prune(['--days' => 30]);

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('nichts zu tun', $ausgabe);
    }

    // Randfall 2
    public function test_nicht_loeschbare_datei_wird_gewarnt_der_rest_geloescht_exit_nicht_null(): void
    {
        if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
            $this->markTestSkipped('Als root gibt es kein Verzeichnis ohne Schreibrecht.');
        }

        $alt = $this->tage(60);
        $this->datei('gesperrt/fest.log', $alt);
        $this->datei('frei.log', $alt);
        $this->datei('.gitignore', $alt);
        chmod($this->logs().'/gesperrt', 0555);

        [$exit, $ausgabe] = $this->prune(['--days' => 30]);

        $this->assertNotSame(0, $exit);
        $this->assertStringContainsString('fest.log', $ausgabe);
        $this->assertFileDoesNotExist($this->logs().'/frei.log');
        $this->assertFileExists($this->logs().'/gesperrt/fest.log');
        $this->assertFileExists($this->logs().'/.gitignore');
    }

    // Randfall 3
    public function test_folgt_symbolischen_links_nicht_und_loescht_sie_nicht(): void
    {
        $alt = $this->tage(60);
        $extern = $this->basis().'/extern';
        mkdir($extern);
        file_put_contents($extern.'/ziel.log', 'x');
        file_put_contents($extern.'/e.log', 'x');
        touch($extern.'/ziel.log', Carbon::now()->getTimestamp() - $alt);
        touch($extern.'/e.log', Carbon::now()->getTimestamp() - $alt);
        $this->datei('normal.log', $alt);
        symlink($extern.'/ziel.log', $this->logs().'/link.log');
        symlink($extern, $this->logs().'/ordner-link');

        [$exit] = $this->prune(['--days' => 30]);

        $this->assertSame(0, $exit);
        // Gegenprobe: normale Datei gelöscht, Aufbau greift
        $this->assertFileDoesNotExist($this->logs().'/normal.log');
        $this->assertTrue(is_link($this->logs().'/link.log'));
        $this->assertTrue(is_link($this->logs().'/ordner-link'));
        $this->assertFileExists($extern.'/ziel.log');
        $this->assertFileExists($extern.'/e.log');
    }

    // Randfall 4
    public function test_grenze_genau_n_tage_bleibt_aelter_wird_geloescht(): void
    {
        $jetzt = Carbon::now()->startOfSecond();
        $this->travelTo($jetzt);
        $grenze = $jetzt->copy()->subDays(30)->getTimestamp();
        foreach (['juenger.log' => $grenze + 1, 'genau.log' => $grenze, 'aelter.log' => $grenze - 1] as $name => $zeit) {
            file_exists($this->logs()) || mkdir($this->logs(), 0777, true);
            file_put_contents($this->logs().'/'.$name, 'x');
            touch($this->logs().'/'.$name, $zeit);
        }

        [$exit] = $this->prune(['--days' => 30]);

        $this->assertSame(0, $exit);
        $this->assertFileDoesNotExist($this->logs().'/aelter.log');
        $this->assertFileExists($this->logs().'/genau.log');
        $this->assertFileExists($this->logs().'/juenger.log');
    }

    // Randfall 5
    public function test_altes_laravel_log_wird_nach_n_tagen_geloescht_ein_junges_bleibt(): void
    {
        $this->datei('laravel.log', $this->tage(40), str_repeat('x', 1024 * 1024));
        $this->datei('laravel-2026-11-18.log', $this->tage(1));

        [$exit] = $this->prune(['--days' => 30]);

        $this->assertSame(0, $exit);
        $this->assertFileDoesNotExist($this->logs().'/laravel.log');
        $this->assertFileExists($this->logs().'/laravel-2026-11-18.log');
    }
}
