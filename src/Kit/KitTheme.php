<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

/**
 * Erlaubte Werte von config('laravelkit.theme') für die Kit-Components.
 */
enum KitTheme: string
{
    case Tailwind = 'tailwind';
    case Bootstrap5 = 'bootstrap5';

    /**
     * @throws UnbekanntesThemeException wenn der Wert kein erlaubtes Theme ist (auch leer oder null)
     */
    public static function ausWert(mixed $wert): self
    {
        $theme = is_string($wert) ? self::tryFrom($wert) : null;

        if ($theme === null) {
            throw UnbekanntesThemeException::fuerWert($wert);
        }

        return $theme;
    }

    /**
     * @return list<string>
     */
    public static function erlaubteWerte(): array
    {
        return array_map(static fn (self $theme): string => $theme->value, self::cases());
    }
}
