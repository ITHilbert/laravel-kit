<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

/**
 * Gemeinsamer Helfer der Doku-Tests zum CHANGELOG (#2038, #2100).
 *
 * Jeder Test liest den Abschnitt „## <x.y.z> – <datum>“ der Version, in der sein Feature erschien. Dieser Abschnitt
 * ändert sich nach dem Release nicht mehr; ein neuer Versionsabschnitt darüber (nächster Release) lässt ihn unberührt.
 * Fehlt der Abschnitt oder der Eintrag darin, wird der Test rot.
 */
trait ChangelogHilfe
{
    private function changelogVersion(string $changelog, string $version): string
    {
        $this->assertSame(
            1,
            preg_match('/^## '.preg_quote($version, '/').'\b.*$/m', $changelog, $treffer, PREG_OFFSET_CAPTURE),
            "Abschnitt „{$version}“ fehlt im CHANGELOG",
        );
        $start = $treffer[0][1];
        $rest = substr($changelog, $start + strlen($treffer[0][0]));
        $ende = preg_match('/^## /m', $rest, $n, PREG_OFFSET_CAPTURE) ? $n[0][1] : strlen($rest);

        return substr($changelog, $start, strlen($treffer[0][0]) + $ende);
    }
}
