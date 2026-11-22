<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

// testebene: Unit-Verhalten, liegt neben dem Helfer
class ChangelogHilfeTest extends TestCase
{
    use ChangelogHilfe;

    private const LOG = "# Changelog\n\n## Unveröffentlicht\n\n## 1.1.0 – 2026-02-01\n### Neu\nfeature-a\n\n## 1.0.0 – 2026-01-01\nfeature-b\n";

    // #2100: ein neuer Versionsabschnitt macht den Abschnitt einer älteren Version nicht rot
    public function test_neuer_versionsabschnitt_aendert_den_gelesenen_abschnitt_nicht(): void
    {
        $vorher = $this->changelogVersion(self::LOG, '1.0.0');
        $nachher = $this->changelogVersion(str_replace('## 1.1.0', "## 1.2.0 – 2026-03-01\nfeature-c\n\n## 1.1.0", self::LOG), '1.0.0');

        $this->assertStringContainsString('feature-b', $vorher);
        $this->assertSame($vorher, $nachher);
        $this->assertStringNotContainsString('feature-a', $vorher);
    }

    // #2100: Gegenprobe — fehlt der Eintrag oder der Abschnitt, bleibt der Test rot
    public function test_fehlender_eintrag_oder_abschnitt_bleibt_rot(): void
    {
        $this->assertStringNotContainsString('feature-b', $this->changelogVersion(self::LOG, '1.1.0'));

        $this->expectException(AssertionFailedError::class);
        $this->changelogVersion(self::LOG, '9.9.9');
    }
}
