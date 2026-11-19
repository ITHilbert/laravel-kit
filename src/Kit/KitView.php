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

        self::auslaufHinweis($theme);

        return 'laravelkit::kit.'.$theme->value.'.'.$baustein;
    }

    /**
     * Meldet ein auslaufendes Theme einmal je Anwendungsinstanz (je Request) als E_USER_DEPRECATED.
     * Das Kit schreibt nicht selbst ins Log; Laravel leitet die Meldung an den Kanal `deprecations`.
     */
    private static function auslaufHinweis(KitTheme $theme): void
    {
        if ($theme === KitTheme::Kern) {
            return;
        }

        $app = app();
        $schluessel = 'laravelkit.auslauf-gemeldet';
        if ($app->bound($schluessel)) {
            return;
        }
        $app->instance($schluessel, true);

        trigger_error(
            "laravel-kit: Das Theme {$theme->value} läuft aus und wird mit einer neuen Major-Version entfernt. "
            .'Wechsle mit LARAVELKIT_THEME=kern auf das Theme kern.',
            E_USER_DEPRECATED,
        );
    }
}
