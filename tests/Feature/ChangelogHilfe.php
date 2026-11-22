<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

/**
 * Gemeinsamer Helfer der Doku-Tests zum CHANGELOG (#2038).
 *
 * Neue Einträge stehen bis zum Release unter „## Unveröffentlicht“ und danach unter „## <x.y.z> – <datum>“.
 * Die Tests lesen deshalb beide Abschnitte: „Unveröffentlicht“ und die jeweils letzte veröffentlichte Version
 * (erster Versions-Abschnitt). Ältere Versionen zählen nicht.
 */
trait ChangelogHilfe
{
    private function changelogAktuell(string $changelog): string
    {
        preg_match_all('/^## .*$/m', $changelog, $treffer, PREG_OFFSET_CAPTURE);
        $ueberschriften = $treffer[0];

        $teile = [];
        $versionGefunden = false;
        $unveroeffentlichtGefunden = false;
        foreach ($ueberschriften as $i => [$titel, $start]) {
            $istUnveroeffentlicht = (bool) preg_match('/^## Unveröffentlicht\s*$/u', $titel);
            $istVersion = ! $versionGefunden && preg_match('/^## \d+\.\d+\.\d+\b/', $titel);
            if (! $istUnveroeffentlicht && ! $istVersion) {
                continue;
            }
            $ende = isset($ueberschriften[$i + 1]) ? $ueberschriften[$i + 1][1] : strlen($changelog);
            $teile[] = substr($changelog, $start, $ende - $start);
            $unveroeffentlichtGefunden = $unveroeffentlichtGefunden || $istUnveroeffentlicht;
            $versionGefunden = $versionGefunden || $istVersion;
        }

        $this->assertTrue($unveroeffentlichtGefunden, 'Abschnitt „Unveröffentlicht“ fehlt im CHANGELOG');
        $this->assertTrue($versionGefunden, 'Abschnitt der letzten veröffentlichten Version fehlt im CHANGELOG');

        return implode("\n", $teile);
    }
}
