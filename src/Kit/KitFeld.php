<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Kit;

use Illuminate\Support\Collection;
use Illuminate\Support\ViewErrorBag;

/**
 * Leitet für die Formular-Kit-Components aus dem Feldnamen id, old()-Wert und Fehlermeldung ab.
 * Feldnamen in Array-Schreibweise (adresse[ort], tags[]) werden für old() und $errors in Punktschreibweise übersetzt.
 */
final class KitFeld
{
    /**
     * Feldname in Punktschreibweise: adresse[ort] → adresse.ort, tags[] → tags.
     */
    public static function punkt(string $name): string
    {
        $ohneListe = str_replace('[]', '', $name);

        return trim(str_replace(['[', ']'], ['.', ''], $ohneListe), '.');
    }

    /**
     * Übergebene id, sonst die id aus dem Namen: adresse[ort] → adresse_ort, tags[] → tags.
     */
    public static function id(string $name, ?string $id = null): string
    {
        if ($id !== null && $id !== '') {
            return $id;
        }

        $ohneListe = str_replace('[]', '', $name);

        return trim(str_replace(['[', ']'], ['_', ''], $ohneListe), '_');
    }

    /**
     * old()-Wert eines Einzelfeldes als Text; nicht vorhanden, ohne Session oder als Array → null.
     */
    public static function alt(string $name): ?string
    {
        $wert = self::altRoh($name);

        return self::alsText($wert);
    }

    /**
     * Erste Fehlermeldung zum Feld aus dem geteilten $errors-Bag; ohne Bag oder ohne Meldung → null.
     */
    public static function fehler(string $name): ?string
    {
        $fehler = view()->shared('errors');

        if (! $fehler instanceof ViewErrorBag) {
            return null;
        }

        $meldung = $fehler->first(self::punkt($name));

        return $meldung === '' ? null : $meldung;
    }

    /**
     * Ausgewählte Werte eines select: übergebenes value, sonst old(); Arrays nur bei Mehrfachauswahl.
     *
     * @return list<string>
     */
    public static function gewaehlt(string $name, mixed $value, bool $mehrfach): array
    {
        $quelle = $value ?? self::altRoh($name);

        if ($quelle instanceof Collection) {
            $quelle = $quelle->all();
        }

        if (is_array($quelle)) {
            if (! $mehrfach) {
                return [];
            }

            $werte = [];
            foreach ($quelle as $eintrag) {
                $text = self::alsText($eintrag);
                if ($text !== null) {
                    $werte[] = $text;
                }
            }

            return $werte;
        }

        $text = self::alsText($quelle);

        return $text === null ? [] : [$text];
    }

    private static function altRoh(string $name): mixed
    {
        $request = request();

        if (! $request->hasSession()) {
            return null;
        }

        return $request->old(self::punkt($name));
    }

    private static function alsText(mixed $wert): ?string
    {
        return is_scalar($wert) || $wert instanceof \Stringable ? (string) $wert : null;
    }
}
