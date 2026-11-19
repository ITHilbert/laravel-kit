<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * #1715: README (Abschnitt „Auslaufend“) und CHANGELOG (Unveröffentlicht → „Deprecated“)
 * nennen die auslaufenden Teile mit Ersatz.
 */
final class AuslaufDokuTest extends TestCase
{
    /** Altes Teil => Ersatz, der in derselben oder der folgenden Zeile stehen muss. */
    private const MIT_ERSATZ = [
        'vue/bootstrap4' => 'vue3/',
        'vue/bootstrap5' => 'vue3/',
        'vue/tailwind' => 'vue3/',
        'vueapp_TW.js' => 'mountInseln',
        'vueapp_B5.js' => 'mountInseln',
        'vueapp_B4.js' => 'mountInseln',
    ];

    private function datei(string $name): string
    {
        $pfad = dirname(__DIR__, 3).'/'.$name;
        $this->assertFileExists($pfad);

        return str_replace("\r\n", "\n", (string) file_get_contents($pfad));
    }

    /** Text ab der Überschrift (Regex) bis zur nächsten Überschrift derselben oder höherer Ebene. */
    private function abschnitt(string $text, string $titelRegex, int $ebene): string
    {
        $ueberschrift = str_repeat('#', $ebene);
        $this->assertSame(
            1,
            preg_match('/^'.$ueberschrift.'\s+'.$titelRegex.'\s*$/mu', $text, $treffer, PREG_OFFSET_CAPTURE),
            "Überschrift {$ueberschrift} {$titelRegex} fehlt",
        );
        $start = $treffer[0][1] + strlen($treffer[0][0]);
        $rest = substr($text, $start);
        $ende = preg_match('/^#{1,'.$ebene.'}\s/m', $rest, $n, PREG_OFFSET_CAPTURE) ? $n[0][1] : strlen($rest);

        return substr($rest, 0, $ende);
    }

    /** Die Blade-Themes bleiben übrig, wenn man die Verzeichnisse und Dateien entfernt, die denselben Namen tragen. */
    private function ohneVerzeichnisse(string $text): string
    {
        return str_replace(['vue/bootstrap4', 'vue/bootstrap5', 'vue/tailwind', 'vueapp_TW.js', 'vueapp_B5.js', 'vueapp_B4.js'], '', $text);
    }

    /** @return array<string, array{string, string}> */
    public static function teile(): array
    {
        $daten = [];
        foreach (self::MIT_ERSATZ as $alt => $ersatz) {
            $daten[$alt] = [$alt, $ersatz];
        }

        return $daten;
    }

    // AK20
    public function test_readme_hat_vor_installation_einen_abschnitt_auslaufend(): void
    {
        $readme = $this->datei('README.md');

        $this->assertSame(1, preg_match('/^## Auslaufend\s*$/m', $readme, $a, PREG_OFFSET_CAPTURE), 'Abschnitt „Auslaufend“ fehlt');
        $this->assertSame(1, preg_match('/^## Installation\s*$/m', $readme, $i, PREG_OFFSET_CAPTURE), 'Gegenprobe: Abschnitt „Installation“ gefunden');
        $this->assertLessThan($i[0][1], $a[0][1], '„Auslaufend“ steht nach „Installation“');
    }

    // AK20
    #[DataProvider('teile')]
    public function test_readme_nennt_jedes_alte_teil_mit_ersatz(string $alt, string $ersatz): void
    {
        $zeilen = explode("\n", $this->abschnitt($this->datei('README.md'), 'Auslaufend', 2));

        $gefunden = false;
        foreach ($zeilen as $nr => $zeile) {
            if (str_contains($zeile, $alt)) {
                $gefunden = true;
                $this->assertStringContainsString($ersatz, $zeile."\n".($zeilen[$nr + 1] ?? ''), "{$alt} ohne Ersatz {$ersatz}");
            }
        }
        $this->assertTrue($gefunden, "{$alt} steht nicht im Abschnitt „Auslaufend“");
    }

    // AK20
    public function test_readme_nennt_blade_themes_ersatz_kern_rueckzug_und_laufzeit_hinweis(): void
    {
        $abschnitt = $this->abschnitt($this->datei('README.md'), 'Auslaufend', 2);
        $rest = $this->ohneVerzeichnisse($abschnitt);

        $this->assertNotSame('', trim($abschnitt), 'Abschnitt ist leer');
        $this->assertStringContainsString('bootstrap5', $rest, 'Blade-Theme bootstrap5');
        $this->assertStringContainsString('tailwind', $rest, 'Blade-Theme tailwind');
        $this->assertStringContainsString('kern', $abschnitt, 'Ersatz: Theme kern');
        $this->assertStringContainsString('neue Seiten', $abschnitt, 'neue Seiten verwenden sie nicht mehr');
        $this->assertStringContainsString('Major', $abschnitt, 'eine neue Major-Version entfernt sie');
        $this->assertStringContainsString('E_USER_DEPRECATED', $abschnitt, 'Laufzeit-Hinweis aus AK18');
        $this->assertStringContainsString('LOG_DEPRECATIONS_CHANNEL', $abschnitt);
    }

    // AK21
    #[DataProvider('teile')]
    public function test_changelog_nennt_unter_unveroeffentlicht_deprecated_jedes_alte_teil(string $alt, string $ersatz): void
    {
        $unveroeffentlicht = $this->abschnitt($this->datei('CHANGELOG.md'), 'Unveröffentlicht', 2);
        $deprecated = $this->abschnitt($unveroeffentlicht, 'Deprecated', 3);

        // Gegenprobe: der Ausschnitt ist nicht der ganze Abschnitt „Unveröffentlicht“
        $this->assertNotSame($unveroeffentlicht, $deprecated);
        $this->assertStringContainsString($alt, $deprecated);
    }

    // AK21
    public function test_changelog_deprecated_nennt_die_blade_themes(): void
    {
        $unveroeffentlicht = $this->abschnitt($this->datei('CHANGELOG.md'), 'Unveröffentlicht', 2);
        $rest = $this->ohneVerzeichnisse($this->abschnitt($unveroeffentlicht, 'Deprecated', 3));

        $this->assertStringContainsString('bootstrap5', $rest);
        $this->assertStringContainsString('tailwind', $rest);
    }
}
