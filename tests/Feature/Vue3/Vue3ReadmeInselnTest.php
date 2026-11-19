<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Vue3;

use PHPUnit\Framework\TestCase;

// testebene: Quelltext-Wächter (Arch) — liest nur die README des Kits, braucht keine App.
/**
 * AK18: Der README-Abschnitt „Vue 3“ nennt die Grenzen der Inseln und den Weg für Komponenten mit Slots.
 */
final class Vue3ReadmeInselnTest extends TestCase
{
    private function abschnitt(string $readme): string
    {
        return preg_match('~^## Vue 3\s*$(.*?)(?=^## |\z)~ms', $readme, $m) === 1 ? $m[1] : '';
    }

    /** @return list<string> Sätze bzw. Zeilen, die alle Begriffe enthalten */
    private function saetze(string $text, array $begriffe): array
    {
        $treffer = [];
        foreach (preg_split('~(?<=[.!?])\s+|\n~', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $satz) {
            foreach ($begriffe as $b) {
                if (stripos($satz, $b) === false) {
                    continue 2;
                }
            }
            $treffer[] = $satz;
        }

        return $treffer;
    }

    private function inselHinweise(string $abschnitt): bool
    {
        return $this->saetze($abschnitt, ['data-props', 'Insel', 'nur']) !== []
            && $this->saetze($abschnitt, ['HTML', 'verworfen']) !== []
            && $this->saetze($abschnitt, ['KitVue3', 'Slots', 'Dialog', 'Tabs', 'Accordion']) !== [];
    }

    // AK18
    public function test_readme_vue3_nennt_insel_grenzen_und_plugin_fuer_slots(): void
    {
        $abschnitt = $this->abschnitt((string) file_get_contents(dirname(__DIR__, 3).'/README.md'));

        $this->assertNotSame('', $abschnitt);
        $this->assertStringContainsString('mountInseln', $abschnitt);
        $this->assertTrue($this->inselHinweise($abschnitt));
    }

    // AK18
    public function test_der_pruefer_erkennt_fehlende_hinweise(): void
    {
        $voll = "## Vue 3\nEine Insel bekommt nur ihre `data-props`. HTML-Inhalt im Insel-Element wird verworfen. Komponenten mit Slots (Dialog, Tabs, Accordion) bindet man über das Plugin `KitVue3` ein.\n## Weiter\n";
        $this->assertTrue($this->inselHinweise($this->abschnitt($voll)));

        $ohneVerwerfen = str_replace('wird verworfen', 'bleibt', $voll);
        $this->assertFalse($this->inselHinweise($this->abschnitt($ohneVerwerfen)));

        $anderer = "## Vue 3\nnichts\n## Weiter\nnur data-props Insel, HTML verworfen, KitVue3 Slots Dialog Tabs Accordion\n";
        $this->assertFalse($this->inselHinweise($this->abschnitt($anderer)));
    }
}
