<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

use Illuminate\Support\Collection;

/**
 * Normalisiert die `options` der Kit-Component combobox (wie die Vue-Version: Einträge mit cbKey und cbCaption).
 */
final class KitCombobox
{
    /**
     * @return list<array{wert: string, text: string}>
     */
    public static function optionen(mixed $options): array
    {
        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        if ($options instanceof Collection) {
            $options = $options->all();
        }

        if (! is_array($options)) {
            return [];
        }

        $liste = [];
        foreach ($options as $eintrag) {
            $eintrag = (array) $eintrag;
            $liste[] = [
                'wert' => self::text($eintrag['cbKey'] ?? ''),
                'text' => self::text($eintrag['cbCaption'] ?? ''),
            ];
        }

        return $liste;
    }

    private static function text(mixed $wert): string
    {
        return is_scalar($wert) || $wert instanceof \Stringable ? (string) $wert : '';
    }
}
