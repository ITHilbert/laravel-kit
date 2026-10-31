<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

use InvalidArgumentException;

/**
 * Konfigurationsfehler: laravelkit.theme enthält keinen erlaubten Wert.
 */
final class UnbekanntesThemeException extends InvalidArgumentException
{
    public static function fuerWert(mixed $wert): self
    {
        $anzeige = match (true) {
            is_string($wert) => '"'.$wert.'"',
            is_scalar($wert) => var_export($wert, true),
            default => get_debug_type($wert),
        };

        return new self(sprintf(
            'Unbekanntes Theme %s in laravelkit.theme (LARAVELKIT_THEME). Erlaubte Werte: %s.',
            $anzeige,
            implode(', ', KitTheme::erlaubteWerte()),
        ));
    }
}
