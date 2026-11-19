<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Logs;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;

/**
 * Löscht `.log`-Dateien unterhalb eines Verzeichnisses, deren Änderungszeit älter als N Tage ist.
 * Symbolische Links werden weder verfolgt noch gelöscht.
 */
final class LogDateienLoeschen
{
    public function __construct(private readonly Filesystem $dateien) {}

    /**
     * @return array{geloescht: list<string>, fehlgeschlagen: list<string>} relative Pfade mit `/`;
     *                                                                      bei $trocken stehen die betroffenen Dateien unter `geloescht`
     */
    public function ausfuehren(string $verzeichnis, int $tage, bool $trocken = false): array
    {
        $ergebnis = ['geloescht' => [], 'fehlgeschlagen' => []];

        if (! is_dir($verzeichnis) || is_link($verzeichnis)) {
            return $ergebnis;
        }

        $grenze = Carbon::now()->subDays($tage)->getTimestamp();
        $basis = rtrim($verzeichnis, '/');

        foreach ($this->logDateien($basis, '') as $relativ) {
            $pfad = $basis.'/'.$relativ;
            $zeit = filemtime($pfad);

            if ($zeit === false || $zeit >= $grenze) {
                continue;
            }

            if ($trocken || $this->dateien->delete($pfad)) {
                $ergebnis['geloescht'][] = $relativ;
            } else {
                $ergebnis['fehlgeschlagen'][] = $relativ;
            }
        }

        return $ergebnis;
    }

    /**
     * @return list<string>
     */
    private function logDateien(string $basis, string $unter): array
    {
        $ordner = $unter === '' ? $basis : $basis.'/'.$unter;
        $namen = scandir($ordner);
        $treffer = [];

        foreach ($namen === false ? [] : $namen as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $relativ = $unter === '' ? $name : $unter.'/'.$name;
            $pfad = $basis.'/'.$relativ;

            if (is_link($pfad)) {
                continue;
            }

            if (is_dir($pfad)) {
                array_push($treffer, ...$this->logDateien($basis, $relativ));
            } elseif (is_file($pfad) && str_ends_with($name, '.log')) {
                $treffer[] = $relativ;
            }
        }

        return $treffer;
    }
}
