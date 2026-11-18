<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Facades\Blade;

/**
 * Gemeinsame Helfer der Tests zum Theme `kern` (Spec blade-kern).
 */
trait KernHilfe
{
    private function kernRendern(string $blade): string
    {
        config(['laravelkit.theme' => 'kern']);

        return Blade::render($blade);
    }

    private function dom(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    /** Bedingung „Element trägt Klasse“ als XPath-Prädikat. */
    private static function kl(string $klasse): string
    {
        return "[contains(concat(' ', normalize-space(@class), ' '), ' {$klasse} ')]";
    }

    private function trifft(string $html, string $xpath): bool
    {
        $ergebnis = $this->dom($html)->evaluate($xpath);

        return $ergebnis instanceof DOMNodeList ? $ergebnis->length > 0 : (bool) $ergebnis;
    }

    private function sieHat(string $html, string $xpath): void
    {
        $this->assertTrue($this->trifft($html, $xpath), "XPath trifft nicht: {$xpath}\nHTML: {$html}");
    }

    private function sieHatNicht(string $html, string $xpath): void
    {
        $this->assertFalse($this->trifft($html, $xpath), "XPath trifft unerwartet: {$xpath}\nHTML: {$html}");
    }

    private function text(string $html, string $xpath): string
    {
        $knoten = $this->dom($html)->query($xpath);

        return $knoten !== false && $knoten->length > 0 ? trim($knoten->item(0)->textContent) : '';
    }

    private function attribut(string $html, string $xpath, string $name): ?string
    {
        $knoten = $this->dom($html)->query($xpath);
        if ($knoten === false || $knoten->length === 0) {
            return null;
        }

        return $knoten->item(0)->getAttribute($name);
    }

    /**
     * Klassen, die nicht `kit-…` und kein erlaubter Zustand sind, und Elemente mit mehr als einer `kit-`-Klasse.
     *
     * @return list<string>
     */
    private function fremdeKlassen(string $html): array
    {
        $fremd = [];
        foreach ($this->dom($html)->query('//*[@class]') as $element) {
            $kit = 0;
            foreach (preg_split('/\s+/', trim($element->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) as $klasse) {
                if (str_starts_with($klasse, 'kit-')) {
                    $kit++;
                } elseif (! in_array($klasse, ['is-invalid', 'is-active', 'is-open', 'disabled'], true)) {
                    $fremd[] = $klasse;
                }
            }
            if ($kit > 1) {
                $fremd[] = '(mehr als eine kit-Klasse: '.$element->getAttribute('class').')';
            }
        }

        return $fremd;
    }
}
