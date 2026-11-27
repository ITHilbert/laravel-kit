<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Nacharbeit zum Blade-Lösch-Dialog (Spec loesch-dialog-nacharbeit, #2435): README und Kopplung Skript ↔ Template.
 */
final class DialogDeleteSelektorenTest extends TestCase
{
    use KernHilfe;

    private function paketDatei(string $pfad): string
    {
        $datei = dirname(__DIR__, 3).'/'.$pfad;
        $this->assertFileExists($datei);

        return (string) file_get_contents($datei);
    }

    /** Text ab einer Überschrift/Zeile bis zur nächsten Überschrift derselben oder höherer Ebene. */
    private function abschnitt(string $readme, string $start, string $ende = '/^#{1,4}\s/m'): string
    {
        $pos = strpos($readme, $start);
        $this->assertNotFalse($pos, "README: „{$start}“ fehlt");
        $rest = substr($readme, $pos + strlen($start));
        $teile = preg_split($ende, $rest, 2);

        return (string) $teile[0];
    }

    // AK5
    public function test_readme_nennt_dialog_delete_in_tabelle_skriptliste_und_componentliste(): void
    {
        $readme = $this->paketDatei('README.md');

        $kern = $this->abschnitt($readme, '#### Theme `kern`: Klassen und Skript-Bindung', '/^###\s/m');
        $zeile = null;
        foreach (explode("\n", $kern) as $z) {
            if (str_starts_with($z, '|') && str_contains($z, 'dialog-delete')) {
                $zeile = $z;
            }
        }
        $this->assertNotNull($zeile, 'Tabellenzeile zu `dialog-delete` fehlt');
        foreach (['kit-dialog', '__overlay', '__content', '__title'] as $teil) {
            $this->assertStringContainsString($teil, $zeile);
        }
        // Gegenprobe: eine bestehende Zeile liegt im selben Abschnitt
        $this->assertStringContainsString('| `breadcrumb` |', $kern);

        $skripte = $this->abschnitt($kern, 'Die Skripte binden die Kern-Templates');
        $this->assertStringContainsString('js/loesch-dialog.js', $skripte);
        $this->assertStringContainsString('js/tabs.js', $skripte, 'Gegenprobe: Abschnitt ist die Skriptliste');

        $liste = $this->abschnitt($readme, '### Verfügbare Kit-Components', '/^###?\s/m');
        $this->assertStringContainsString('dialog-delete', $liste);
        $this->assertStringContainsString('button-delete', $liste, 'Gegenprobe: Abschnitt ist die Component-Liste');

        $this->assertMatchesRegularExpression('/vue3\/kit\.css[^\n]*(geladen|eingebunden)[^\n]*(fest|Ebene)|(fest|Ebene)[^\n]*vue3\/kit\.css[^\n]*(geladen|eingebunden)/u', $readme, 'Satz zu `vue3/kit.css` für die feste Ebene fehlt');
    }

    // AK6
    public function test_jeder_selektor_des_skripts_kommt_im_gerenderten_template_vor(): void
    {
        $js = $this->paketDatei('js/loesch-dialog.js');
        $html = $this->kernRendern('<x-kit::dialog-delete route="/kunden/0" />');

        foreach (['[data-kit-delete-dialog]', '[data-kit-delete-bestaetigen]', '[data-kit-delete-abbrechen]'] as $selektor) {
            $this->assertStringContainsString($selektor, $js, "Gegenprobe: Skript nutzt {$selektor}");
            $name = trim($selektor, '[]');
            $this->assertMatchesRegularExpression('/<[^>]*\s'.preg_quote($name, '/').'(\s|=|>)/', $html, "Template liefert {$name} nicht");
        }
        foreach (['data-route', 'hidden'] as $attr) {
            $this->assertStringContainsString($attr, $js, "Gegenprobe: Skript nutzt {$attr}");
            $this->assertMatchesRegularExpression('/<div[^>]*data-kit-delete-dialog[^>]*\s'.preg_quote($attr, '/').'(\s|=|>)/', $html, "Wurzel trägt {$attr} nicht");
        }
    }
}
