<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

use Illuminate\Support\Collection;

/**
 * Normalisiert die `options` der Kit-Components combobox und select (Einträge mit cbKey und cbCaption, beim select auch Schlüssel→Text).
 */
final class KitCombobox
{
    /**
     * Optionen der combobox: Einträge mit cbKey/cbCaption.
     *
     * @return list<array{wert: string, text: string}>
     */
    public static function optionen(mixed $options): array
    {
        $liste = [];
        foreach (self::eintraege($options) as $eintrag) {
            $liste[] = self::ausEintrag($eintrag);
        }

        return $liste;
    }

    /**
     * Optionen des select: Einträge mit cbKey/cbCaption wie optionen() oder Schlüssel→Text (['a' => 'Alpha']).
     *
     * @return list<array{wert: string, text: string}>
     */
    public static function auswahl(mixed $options): array
    {
        $liste = [];
        foreach (self::eintraege($options) as $schluessel => $eintrag) {
            $liste[] = is_array($eintrag) || is_object($eintrag)
                ? self::ausEintrag($eintrag)
                : ['wert' => (string) $schluessel, 'text' => self::text($eintrag)];
        }

        return $liste;
    }

    /**
     * Array, Collection oder JSON-Text als Array; alles andere ergibt keine Einträge.
     *
     * @return array<array-key, mixed>
     */
    private static function eintraege(mixed $options): array
    {
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        if ($options instanceof Collection) {
            $options = $options->all();
        }

        return is_array($options) ? $options : [];
    }

    /**
     * @return array{wert: string, text: string}
     */
    private static function ausEintrag(mixed $eintrag): array
    {
        $eintrag = (array) $eintrag;

        return [
            'wert' => self::text($eintrag['cbKey'] ?? ''),
            'text' => self::text($eintrag['cbCaption'] ?? ''),
        ];
    }

    private static function text(mixed $wert): string
    {
        return is_scalar($wert) || $wert instanceof \Stringable ? (string) $wert : '';
    }
}
