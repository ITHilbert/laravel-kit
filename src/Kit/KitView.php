<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

/**
 * Löst eine Kit-Component zur Laufzeit auf das Template des konfigurierten Themes auf.
 * Einzige Stelle, an der das Theme gelesen und geprüft wird.
 */
final class KitView
{
    /**
     * @throws UnbekanntesThemeException
     */
    public static function fuer(string $baustein): string
    {
        $theme = KitTheme::ausWert(config('laravelkit.theme'));

        return 'laravelkit::kit.'.$theme->value.'.'.$baustein;
    }
}
