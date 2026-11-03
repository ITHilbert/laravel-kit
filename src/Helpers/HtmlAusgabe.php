<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Helpers;

use Illuminate\Support\HtmlString;

/**
 * Gemeinsame HTML-Ausgabe von HForm und HButton: Escaping, zusätzliche Attribute, HtmlString.
 */
trait HtmlAusgabe
{
    /**
     * Gültiger Attribut-Schlüssel: keine Leerzeichen, Anführungszeichen, `>`, `/`, `=` oder Steuerzeichen.
     */
    private const GUELTIGER_SCHLUESSEL = '/^[^\s"\'>\/=\x00-\x1f\x7f]+\z/';

    /**
     * Escaped einen Wert für HTML (Htmlable bleibt unverändert, null ergibt '').
     */
    protected static function esc(mixed $value): string
    {
        return e($value);
    }

    /**
     * Formatiert einen String in HTML Code
     */
    protected static function toHtmlString(string $html): HtmlString
    {
        return new HtmlString($html);
    }

    /**
     * Formatiert die Werte im Array zu HTML-Attributen (`schluessel="wert" ` je Paar, Wert escaped).
     *
     * Nicht ausgegeben werden – Vergleich ohne Groß-/Kleinschreibung:
     * - ungültige Schlüssel,
     * - Schlüssel, die ein von der Methode fest gesetztes Attribut ($fest) doppeln,
     * - jede Wiederholung eines Schlüssels in anderer Schreibweise (das erste Paar gilt, wie im Browser).
     *
     * @param  array<array-key, mixed>  $options
     * @param  list<string>  $fest  Attribute, die die aufrufende Methode am selben Element selbst setzt
     */
    protected static function attributes(array $options = [], array $fest = []): string
    {
        $vergeben = array_fill_keys(array_map(strtolower(...), $fest), true);

        $attr = '';
        foreach ($options as $key => $value) {
            $key = (string) $key;
            $name = strtolower($key);
            if (! preg_match(self::GUELTIGER_SCHLUESSEL, $key) || isset($vergeben[$name])) {
                continue;
            }
            $vergeben[$name] = true;
            $attr .= $key.'="'.self::esc($value).'" ';
        }

        return $attr;
    }
}
