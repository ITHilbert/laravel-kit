<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\Feature\ChangelogHilfe;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Blade-Component `<x-kit::dialog-delete>` (Spec loesch-dialog-blade, #2298): Markup je Theme, CSS, README, CHANGELOG.
 * Die Zustandslogik prüft tests/js/loeschDialog.test.mjs.
 */
final class DialogDeleteTest extends TestCase
{
    use ChangelogHilfe;
    use KernHilfe;

    private const BLADE = '<x-kit::dialog-delete titel="Kunde löschen" body="Wirklich löschen?" route="/kunden/0" />';

    private const WURZEL = '//div[@data-kit-delete-dialog]';

    private function paketDatei(string $pfad): string
    {
        $datei = dirname(__DIR__, 3).'/'.$pfad;
        $this->assertFileExists($datei);

        return (string) file_get_contents($datei);
    }

    private function mitTheme(string $theme, string $blade, array $daten = []): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade, $daten);
    }

    // AK1
    public function test_kern_rendert_dialog_mit_rollen_und_genau_zwei_buttons(): void
    {
        $html = $this->kernRendern(self::BLADE);
        $w = self::WURZEL.self::kl('kit-dialog');

        $this->sieHat($html, $w);
        $this->assertSame('/kunden/0', $this->attribut($html, $w, 'data-route'));
        $this->sieHat($html, $w.'[@hidden]');

        $inhalt = $w.'/div'.self::kl('kit-dialog__content').'[@role="dialog"][@aria-modal="true"]';
        $this->sieHat($html, $inhalt);
        $this->assertSame('Kunde löschen', $this->text($html, $inhalt.'/h2'.self::kl('kit-dialog__title')));
        $this->assertSame('Wirklich löschen?', $this->text($html, $inhalt.'/p'));

        $id = $this->attribut($html, $inhalt.'/h2', 'id');
        $this->assertNotSame('', (string) $id, 'Überschrift braucht eine id');
        $this->assertSame($id, $this->attribut($html, $inhalt, 'aria-labelledby'));

        $this->sieHat($html, $w.'/div'.self::kl('kit-dialog__overlay').'[@data-kit-delete-abbrechen]');
        // genau zwei Buttons im ganzen Baustein (Gegenprobe: beide sind die erwarteten)
        $this->assertSame(2, (int) $this->dom($html)->evaluate('count(//button)'));
        $knopf = 'button'.self::kl('kit-button').'[@type="button"]';
        $this->assertSame('Abbrechen', $this->text($html, "{$inhalt}//{$knopf}[@data-variant='cancel'][@data-kit-delete-abbrechen]"));
        $this->assertSame('Löschen', $this->text($html, "{$inhalt}//{$knopf}[@data-variant='delete'][@data-kit-delete-bestaetigen]"));

        $this->assertSame([], $this->fremdeKlassen($html));
    }

    // AK2
    public function test_kern_hat_genau_ein_lösch_formular_mit_csrf_und_delete_ohne_action(): void
    {
        // Im Testbench-Render läuft keine Session: ohne Token gibt @csrf ein leeres value aus
        $this->startSession();
        session()->put('_token', 'abc123');
        $html = $this->kernRendern(self::BLADE);

        $this->assertSame(1, (int) $this->dom($html)->evaluate('count(//form[@id="formDelete"])'));
        $this->assertSame(1, (int) $this->dom($html)->evaluate('count(//form)'));
        $form = '//form[@id="formDelete"]';
        $this->assertSame('POST', strtoupper((string) $this->attribut($html, $form, 'method')));
        $this->sieHat($html, $form.'//input[@name="_token"]');
        $this->assertSame('abc123', $this->attribut($html, $form.'//input[@name="_token"]', 'value'));
        $this->sieHat($html, $form.'//input[@name="_method"][@value="DELETE"]');
        $this->sieHatNicht($html, $form.'[@action]');
    }

    // AK3
    public function test_werte_werden_escaped_ausgegeben(): void
    {
        $html = $this->mitTheme('kern', '<x-kit::dialog-delete :titel="$t" :body="$b" route="/x/0" />', ['t' => '<b>X</b>', 'b' => 'a & b']);

        $this->assertSame('<b>X</b>', $this->text($html, '//h2'));
        $this->assertSame('a & b', $this->text($html, '//div[@role="dialog"]/p'));
        // Gegenprobe: das Rendern lief, und es gibt kein Markup aus den Werten
        $this->sieHat($html, self::WURZEL);
        $this->sieHatNicht($html, '//h2//b');
        $this->assertStringContainsString('&lt;b&gt;X&lt;/b&gt;', $html);
    }

    // AK4
    public function test_kern_ohne_titel_und_body_zeigt_die_standardwerte(): void
    {
        $html = $this->kernRendern('<x-kit::dialog-delete route="/kunden/0" />');

        $this->sieHat($html, self::WURZEL);
        $this->assertSame('Löschen', $this->text($html, '//h2'));
        $this->assertSame('Diesen Eintrag wirklich löschen?', $this->text($html, '//div[@role="dialog"]/p'));
    }

    // AK5
    #[DataProvider('eingefroreneThemes')]
    public function test_eingefrorene_themes_geben_die_vue_insel_und_das_formular_aus(string $theme): void
    {
        $html = $this->mitTheme($theme, self::BLADE);

        $insel = '//div[@data-vue="dialog-delete"][@data-props]';
        $this->sieHat($html, $insel);
        $props = json_decode((string) $this->attribut($html, $insel, 'data-props'), true);
        $this->assertSame(['titel' => 'Kunde löschen', 'body' => 'Wirklich löschen?', 'route' => '/kunden/0'], array_intersect_key($props, array_flip(['titel', 'body', 'route'])));

        $this->sieHatNicht($html, '//*[@data-kit-delete-dialog]');
        $form = '//form[@id="formDelete"]';
        $this->assertSame(1, (int) $this->dom($html)->evaluate('count(//form[@id="formDelete"])'));
        $this->assertSame('POST', strtoupper((string) $this->attribut($html, $form, 'method')));
        $this->sieHat($html, $form.'//input[@name="_token"]');
        $this->sieHat($html, $form.'//input[@name="_method"][@value="DELETE"]');
        $this->sieHatNicht($html, $form.'[@action]');
        // Formular folgt der Insel
        $this->sieHat($html, $insel.'/following-sibling::form[@id="formDelete"]');
    }

    /** @return array<string, array{string}> */
    public static function eingefroreneThemes(): array
    {
        return ['bootstrap5' => ['bootstrap5'], 'tailwind' => ['tailwind']];
    }

    // AK10
    public function test_css_blendet_hidden_aus_und_zeigt_offenen_dialog_als_feste_ebene(): void
    {
        $css = $this->paketDatei('vue3/kit.css');

        // Gegenprobe: der Regel-Leser findet eine bekannte Regel
        $this->assertMatchesRegularExpression('/position:\s*fixed/', $this->regel($css, ['.kit-dialog__overlay']) ?? '');

        $versteckt = $this->regel($css, ['.kit-dialog[hidden]']);
        $this->assertNotNull($versteckt, 'Regel .kit-dialog[hidden] fehlt');
        $this->assertMatchesRegularExpression('/display:\s*none/', $versteckt);

        $offen = $this->regel($css, ['.kit-dialog', '.kit-dialog:not([hidden])']);
        $this->assertNotNull($offen, 'Regel für den sichtbaren .kit-dialog fehlt');
        $this->assertMatchesRegularExpression('/position:\s*fixed/', $offen);
        $this->assertMatchesRegularExpression('/inset:\s*0|top:\s*0[^}]*left:\s*0/s', $offen);

        $inhalt = $this->regel($css, ['.kit-dialog__content']) ?? '';
        $this->assertMatchesRegularExpression('/top:\s*50%/', $inhalt);
        $this->assertMatchesRegularExpression('/left:\s*50%/', $inhalt);
    }

    // AK11
    public function test_readme_beschreibt_die_component_und_changelog_nennt_sie_unter_unveroeffentlicht(): void
    {
        $readme = $this->paketDatei('README.md');
        $teile = preg_split('/^###\s+Lösch-Button und Lösch-Dialog\s*$/m', $readme);
        $this->assertCount(2, $teile, 'README-Abschnitt „Lösch-Button und Lösch-Dialog“ fehlt');
        $abschnitt = preg_split('/^#{1,3}\s/m', $teile[1])[0];

        $this->assertStringContainsString('<x-kit::dialog-delete', $abschnitt);
        $this->assertStringContainsString('initLoeschDialog', $abschnitt);
        $this->assertStringContainsString('#formDelete', $abschnitt);
        foreach (['titel', 'body', 'route'] as $prop) {
            $this->assertStringContainsString("`{$prop}`", $abschnitt);
        }
        // Gegenprobe: der Abschnitt ist nicht die ganze README
        $this->assertLessThan(strlen($readme), strlen($abschnitt));

        $neu = $this->changelogVersion($this->paketDatei('CHANGELOG.md'), 'Unveröffentlicht');
        $this->assertMatchesRegularExpression('/^- Neu: `<x-kit::dialog-delete>`.*#2298/m', $neu);
        $this->assertStringNotContainsString('## 1.11.0', $neu);
    }

    // AK12
    #[DataProvider('alleThemes')]
    public function test_baustein_dialog_delete_steht_in_der_liste_aller_themes(string $theme): void
    {
        $this->assertFileExists(dirname(__DIR__, 3)."/src/Views/kit/{$theme}/dialog-delete.blade.php");
        $this->assertFileExists(dirname(__DIR__, 3).'/src/Views/components/kit/dialog-delete.blade.php');
        // Gegenprobe: ein bestehender Baustein liegt am selben Ort
        $this->assertFileExists(dirname(__DIR__, 3)."/src/Views/kit/{$theme}/button-delete.blade.php");
    }

    /** @return array<string, array{string}> */
    public static function alleThemes(): array
    {
        return ['kern' => ['kern'], 'bootstrap5' => ['bootstrap5'], 'tailwind' => ['tailwind']];
    }

    /** Rumpf der ersten Regel, deren Selektorliste einen der Selektoren genau enthält (nur flache Regeln). */
    private function regel(string $css, array $selektoren): ?string
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css) ?? '';
        if (preg_match_all('/([^{}]+)\{([^{}]*)\}/', $css, $treffer, PREG_SET_ORDER) < 1) {
            return null;
        }
        foreach ($treffer as [, $liste, $rumpf]) {
            foreach (array_map('trim', explode(',', $liste)) as $selektor) {
                if (in_array($selektor, $selektoren, true)) {
                    return $rumpf;
                }
            }
        }

        return null;
    }
}
