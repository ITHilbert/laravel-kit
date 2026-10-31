<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

use DateTimeInterface;

/**
 * Formatierung der Anzeige-Bausteine show-euro und show-date (wie die Vue-Komponenten).
 */
final class KitAnzeige
{
    public static function euro(mixed $wert): string
    {
        if (! is_numeric($wert)) {
            return '';
        }

        return number_format((float) $wert, 2, ',', '.').' €';
    }

    public static function datum(mixed $wert): string
    {
        if ($wert instanceof DateTimeInterface) {
            return $wert->format('d.m.Y');
        }

        if (! is_string($wert) || $wert === '') {
            return '';
        }

        $teile = explode('-', $wert);

        return count($teile) === 3 ? $teile[2].'.'.$teile[1].'.'.$teile[0] : $wert;
    }
}
