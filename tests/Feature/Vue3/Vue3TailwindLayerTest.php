<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Vue3;

use PHPUnit\Framework\TestCase;

// testebene: Quelltext-Wächter (Arch) — liest nur die Theme-Datei des Kits, braucht keine App.
/**
 * AK15: Alle Regeln für `kit-`-Klassen in `vue3/themes/tailwind.css` stehen in `@layer components`.
 */
final class Vue3TailwindLayerTest extends TestCase
{
    /**
     * Top-Level-Blöcke, die `.kit-` enthalten, aber nicht `@layer components` sind.
     *
     * @return list<string> Köpfe der Blöcke
     */
    private function kitRegelnAusserhalb(string $css): array
    {
        $css = (string) preg_replace('~/\*.*?\*/~s', '', $css);
        $aussen = [];
        $tiefe = 0;
        $kopf = '';
        $start = 0;
        $anfang = 0;
        $laenge = strlen($css);
        for ($i = 0; $i < $laenge; $i++) {
            $z = $css[$i];
            if ($tiefe === 0 && $z === ';') {
                $kopf = '';
                $anfang = $i + 1;
            } elseif ($z === '{') {
                if ($tiefe === 0) {
                    $kopf = trim(substr($css, $anfang, $i - $anfang));
                    $start = $i;
                }
                $tiefe++;
            } elseif ($z === '}') {
                $tiefe--;
                if ($tiefe === 0) {
                    $block = $kopf.substr($css, $start, $i - $start + 1);
                    if (preg_match('~^@layer\s+components$~', $kopf) !== 1 && str_contains($block, '.kit-')) {
                        $aussen[] = $kopf;
                    }
                    $anfang = $i + 1;
                }
            }
        }

        return $aussen;
    }

    // AK15
    public function test_alle_kit_regeln_des_tailwind_themes_liegen_in_layer_components(): void
    {
        $css = (string) file_get_contents(dirname(__DIR__, 3).'/vue3/themes/tailwind.css');

        $this->assertMatchesRegularExpression('~@layer\s+components\s*\{~', $css);
        $this->assertStringContainsString('.kit-', $css);
        $this->assertSame([], $this->kitRegelnAusserhalb($css));
    }

    // AK15
    public function test_der_pruefer_erkennt_regeln_ausserhalb_von_layer_components(): void
    {
        $this->assertSame(['.kit-a'], $this->kitRegelnAusserhalb('.kit-a { color: red; }'));
        $this->assertSame(['@layer utilities'], $this->kitRegelnAusserhalb('@layer utilities { .kit-a { color: red; } }'));
        $this->assertSame(['@media (min-width: 1px)'], $this->kitRegelnAusserhalb('@media (min-width: 1px) { .kit-a { color: red; } }'));
        $this->assertSame(['.kit-b:hover'], $this->kitRegelnAusserhalb("@layer components { .kit-a { color: red; } }\n.kit-b:hover { color: blue; }"));
        $this->assertSame([], $this->kitRegelnAusserhalb("@reference \"tailwindcss\";\n@layer components { .kit-a { @apply p-1; } .kit-a.is-open { color: red; } @media (min-width: 1px) { .kit-b { color: red; } } }"));
    }
}
