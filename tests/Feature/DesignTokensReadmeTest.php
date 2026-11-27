<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

// testebene: Quelltext-Wächter (Arch) — liest nur README und CHANGELOG des Kits, braucht keine App.
/**
 * AK4: README-Abschnitt „Design-Tokens“. AK9: CHANGELOG „Unveröffentlicht“ nennt die neuen Dateien und die Themes.
 */
#[Group('arch')]
final class DesignTokensReadmeTest extends TestCase
{
    private function abschnitt(string $text, string $ueberschrift): string
    {
        $muster = '~^## '.preg_quote($ueberschrift, '~').'\s*$(.*?)(?=^## |\z)~ms';

        return preg_match($muster, $text, $m) === 1 ? $m[1] : '';
    }

    /** @return list<string> Sätze bzw. Zeilen, die alle Begriffe enthalten (ohne Groß-/Kleinschreibung) */
    private function saetze(string $text, array $begriffe): array
    {
        $treffer = [];
        foreach (preg_split('~(?<=[.!?])\s+|\n~', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $satz) {
            foreach ($begriffe as $b) {
                if (stripos($satz, $b) === false) {
                    continue 2;
                }
            }
            $treffer[] = $satz;
        }

        return $treffer;
    }

    /** @return list<string> Gründe, warum der README-Abschnitt AK4 verfehlt; leer = erfüllt */
    private function readmeMaengel(string $abschnitt): array
    {
        $maengel = [];
        if ($abschnitt === '') {
            return ['Abschnitt „Design-Tokens“ fehlt'];
        }
        foreach (['css/ith-tokens.css', 'css/ith-theme.css'] as $datei) {
            if (! str_contains($abschnitt, $datei)) {
                $maengel[] = "nennt $datei nicht";
            }
        }
        if (preg_match_all('~^```~m', $abschnitt) < 6) {
            $maengel[] = 'weniger als drei Codebeispiele';
        }
        foreach (['Tailwind', 'Vite', 'Sass', 'Bootstrap', 'Klartext'] as $wort) {
            if (stripos($abschnitt, $wort) === false) {
                $maengel[] = "Einbindebeispiel ohne Stichwort $wort";
            }
        }
        if (! str_contains($abschnitt, 'wiki.lan/it-hilbert/corporate-design')) {
            $maengel[] = 'Wiki-Seite als Quelle fehlt';
        }
        if ($this->saetze($abschnitt, ['Werte', 'zuerst', 'Wiki', 'ändern']) === []) {
            $maengel[] = '„Werte zuerst im Wiki ändern“ fehlt';
        }
        if (! str_contains($abschnitt, '@fontsource/inter')) {
            $maengel[] = '@fontsource/inter fehlt';
        }
        foreach (['400', '500', '600', '700'] as $schnitt) {
            if ($this->saetze($abschnitt, ['Inter', $schnitt]) === [] && ! preg_match('~@fontsource/inter/'.$schnitt.'\.css~', $abschnitt)) {
                $maengel[] = "Schnitt $schnitt fehlt";
            }
        }
        $verbot = array_filter(
            $this->saetze($abschnitt, ['Google']),
            fn (string $s): bool => stripos($s, 'CDN') !== false && preg_match('~nicht|kein|verbot|nie~i', $s) === 1,
        );
        if ($verbot === []) {
            $maengel[] = 'Verbot des Google-Fonts-CDN fehlt';
        }

        return $maengel;
    }

    /** @return list<string> */
    private function changelogMaengel(string $abschnitt): array
    {
        $maengel = [];
        if ($abschnitt === '') {
            return ['Abschnitt „Unveröffentlicht“ fehlt'];
        }
        foreach (['ith-tokens.css', 'ith-theme.css'] as $datei) {
            if (! str_contains($abschnitt, $datei)) {
                $maengel[] = "nennt $datei nicht";
            }
        }
        if (preg_match('~vue ?3|vue3~i', $abschnitt) !== 1) {
            $maengel[] = 'Umstellung des Vue-3-Themes nicht genannt';
        }
        if (preg_match('~\bkern\b~i', $abschnitt) !== 1) {
            $maengel[] = 'Umstellung des Blade-Themes kern nicht genannt';
        }

        return $maengel;
    }

    private function datei(string $name): string
    {
        return (string) file_get_contents(dirname(__DIR__, 2).'/'.$name);
    }

    // AK4
    public function test_readme_abschnitt_design_tokens_ist_vollstaendig(): void
    {
        $this->assertSame([], $this->readmeMaengel($this->abschnitt($this->datei('README.md'), 'Design-Tokens')));
    }

    // AK9
    public function test_changelog_unveroeffentlicht_nennt_dateien_und_themes(): void
    {
        $this->assertSame([], $this->changelogMaengel($this->abschnitt($this->datei('CHANGELOG.md'), '1.13.0 – 2026-11-27')));
    }

    // AK4, AK9 (Gegenprobe der Prüfer)
    public function test_die_pruefer_erkennen_vollstaendige_und_fehlende_abschnitte(): void
    {
        $block = "```css\nx\n```\n";
        $voll = "## Design-Tokens\nDateien `css/ith-tokens.css` und `css/ith-theme.css`. Quelle: https://wiki.lan/it-hilbert/corporate-design. "
            ."Werte zuerst im Wiki ändern.\n\nTailwind/Vite:\n$block\nSass/Bootstrap:\n$block\nKlartext-CSS:\n$block\n"
            ."Die Schrift Inter wird selbst gehostet über `@fontsource/inter` mit den Schnitten 400, 500, 600, 700. Das Google-Fonts-CDN ist verboten.\n## Weiter\n";
        $this->assertSame([], $this->readmeMaengel($this->abschnitt($voll, 'Design-Tokens')));

        foreach (['@fontsource/inter' => 'fontsource', '600, 700' => '600', 'ist verboten' => 'ist erlaubt', 'Werte zuerst im Wiki ändern' => 'Werte im Wiki'] as $alt => $neu) {
            $kaputt = str_replace($alt, $neu, $voll);
            $this->assertNotSame([], $this->readmeMaengel($this->abschnitt($kaputt, 'Design-Tokens')), $alt);
        }
        $this->assertNotSame([], $this->readmeMaengel($this->abschnitt(str_replace($block, '', $voll), 'Design-Tokens')));
        $this->assertNotSame([], $this->readmeMaengel($this->abschnitt("## Anderes\n".$voll, 'Nichts')));

        $log = "## Unveröffentlicht\n- Neu: `css/ith-tokens.css`, `css/ith-theme.css`; Themes Vue 3 und Blade `kern` auf Tokens umgestellt.\n## 1.0.0\n";
        $this->assertSame([], $this->changelogMaengel($this->abschnitt($log, 'Unveröffentlicht')));
        $this->assertNotSame([], $this->changelogMaengel($this->abschnitt(str_replace('ith-theme.css', 'x.css', $log), 'Unveröffentlicht')));
        $nurAlt = "## Unveröffentlicht\nnichts\n## 1.0.0\n- `css/ith-tokens.css` ith-theme.css Vue 3 kern\n";
        $this->assertNotSame([], $this->changelogMaengel($this->abschnitt($nurAlt, 'Unveröffentlicht')));
    }
}
