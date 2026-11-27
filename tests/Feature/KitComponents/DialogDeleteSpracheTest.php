<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\Feature\ChangelogHilfe;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Sprachdateien der Component `<x-kit::dialog-delete>` (Spec dialog-delete-sprache, #2451).
 * Markup, Selektoren und Skripte prüfen DialogDeleteTest und DialogDeleteSelektorenTest.
 */
final class DialogDeleteSpracheTest extends TestCase
{
    use ChangelogHilfe;
    use KernHilfe;

    private const INSEL = '//div[@data-vue="dialog-delete"][@data-props]';

    private function mitSprache(string $sprache, string $theme, string $blade): string
    {
        app()->setLocale($sprache);
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade);
    }

    /** @return array<string, mixed> */
    private function props(string $html): array
    {
        $this->sieHat($html, self::INSEL);

        return json_decode((string) $this->attribut($html, self::INSEL, 'data-props'), true);
    }

    private function paketDatei(string $pfad): string
    {
        $datei = dirname(__DIR__, 3).'/'.$pfad;
        $this->assertFileExists($datei);

        return (string) file_get_contents($datei);
    }

    // AK1
    public function test_kern_buttons_folgen_der_app_sprache(): void
    {
        $blade = '<x-kit::dialog-delete route="/k/0" />';
        $abbrechen = '//button[@data-kit-delete-abbrechen][@data-variant="cancel"]';
        $loeschen = '//button[@data-kit-delete-bestaetigen]';

        $en = $this->mitSprache('en', 'kern', $blade);
        $this->assertSame('Cancel', $this->text($en, $abbrechen));
        $this->assertSame('Delete', $this->text($en, $loeschen));

        $de = $this->mitSprache('de', 'kern', $blade);
        $this->assertSame('Abbrechen', $this->text($de, $abbrechen));
        $this->assertSame('Löschen', $this->text($de, $loeschen));
    }

    // AK2
    public function test_kern_titel_und_text_ohne_props_kommen_aus_der_sprachdatei(): void
    {
        $blade = '<x-kit::dialog-delete route="/k/0" />';
        $absatz = '//div[@role="dialog"]/p';

        $en = $this->mitSprache('en', 'kern', $blade);
        $this->assertSame('Delete', $this->text($en, '//h2'));
        $this->assertSame('Really delete this entry?', $this->text($en, $absatz));

        $de = $this->mitSprache('de', 'kern', $blade);
        $this->assertSame('Löschen', $this->text($de, '//h2'));
        $this->assertSame('Diesen Eintrag wirklich löschen?', $this->text($de, $absatz));
    }

    // AK2
    #[DataProvider('eingefroreneThemes')]
    public function test_vue_themes_ohne_props_tragen_die_sprachdatei_in_data_props(string $theme): void
    {
        $blade = '<x-kit::dialog-delete route="/k/0" />';

        $en = $this->props($this->mitSprache('en', $theme, $blade));
        $this->assertSame(['Delete', 'Really delete this entry?'], [$en['titel'], $en['body']]);

        $de = $this->props($this->mitSprache('de', $theme, $blade));
        $this->assertSame(['Löschen', 'Diesen Eintrag wirklich löschen?'], [$de['titel'], $de['body']]);
    }

    // AK3
    #[DataProvider('alleThemes')]
    public function test_gesetzte_props_gewinnen_gegen_die_sprachdatei(string $theme): void
    {
        $html = $this->mitSprache('en', $theme, '<x-kit::dialog-delete titel="Kunde löschen" body="Wirklich?" route="/k/0" />');

        if ($theme === 'kern') {
            $this->assertSame('Kunde löschen', $this->text($html, '//h2'));
            $this->assertSame('Wirklich?', $this->text($html, '//div[@role="dialog"]/p'));
        } else {
            $props = $this->props($html);
            $this->assertSame(['Kunde löschen', 'Wirklich?'], [$props['titel'], $props['body']]);
        }

        // Gegenprobe: ohne Props greift in derselben Sprache die Sprachdatei
        $ohne = $this->mitSprache('en', $theme, '<x-kit::dialog-delete route="/k/0" />');
        $titel = $theme === 'kern' ? $this->text($ohne, '//h2') : $this->props($ohne)['titel'];
        $this->assertSame('Delete', $titel);
    }

    // AK4
    public function test_sprachdateien_de_und_en_haben_dieselben_schluessel_und_sind_uebersetzbar(): void
    {
        $schluessel = ['abbrechen', 'body', 'loeschen', 'titel'];
        $basis = dirname(__DIR__, 3).'/src/Lang';

        foreach (['de', 'en'] as $sprache) {
            $this->assertFileExists("{$basis}/{$sprache}/dialog.php");
            $text = require "{$basis}/{$sprache}/dialog.php";
            $this->assertIsArray($text);
            $sortiert = array_keys($text);
            sort($sortiert);
            $this->assertSame($schluessel, $sortiert, "Schlüssel {$sprache}");

            foreach ($schluessel as $name) {
                $uebersetzt = trans("laravelkit::dialog.{$name}", [], $sprache);
                $this->assertNotSame("laravelkit::dialog.{$name}", $uebersetzt, "{$sprache}/{$name} wird vom ServiceProvider nicht geladen");
                $this->assertNotSame('', $uebersetzt);
            }
        }
    }

    // AK5
    public function test_readme_nennt_die_sprachdateien_und_changelog_hat_abschnitt_1_14_0(): void
    {
        $readme = $this->paketDatei('README.md');
        $teile = preg_split('/^###\s+Lösch-Button und Lösch-Dialog\s*$/m', $readme);
        $this->assertCount(2, $teile, 'README-Abschnitt „Lösch-Button und Lösch-Dialog“ fehlt');
        $abschnitt = preg_split('/^#{1,3}\s/m', $teile[1])[0];

        $this->assertStringContainsString('<x-kit::dialog-delete', $abschnitt);
        $this->assertStringContainsString('laravelkit::dialog.', $abschnitt);
        $this->assertStringContainsString('lang/vendor/laravelkit', $abschnitt);

        $neu = $this->changelogVersion($this->paketDatei('CHANGELOG.md'), '1.14.0');
        $this->assertMatchesRegularExpression('/#2451/', $neu);
    }

    /** @return array<string, array{string}> */
    public static function eingefroreneThemes(): array
    {
        return ['bootstrap5' => ['bootstrap5'], 'tailwind' => ['tailwind']];
    }

    /** @return array<string, array{string}> */
    public static function alleThemes(): array
    {
        return ['kern' => ['kern'], ...self::eingefroreneThemes()];
    }
}
