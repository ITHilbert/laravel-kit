<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Kontakt-Leiste (Telefon, E-Mail, WhatsApp): Markup, Config und CSS-Inhalt.
 * Darstellung (AK8, AK9) prüft der Moderator im Browser.
 */
final class KontaktLeisteTest extends TestCase
{
    private const VOLL = [
        'aktiv' => true,
        'telefon' => '+49 4344 3899378',
        'telefon_anzeige' => '+49 4344 - 389 937 8',
        'email' => 'info@example.com',
        'whatsapp' => null,
        'whatsapp_text' => 'Hallo, ich habe eine Frage',
    ];

    /** @param  array<string, mixed>  $config */
    private function leiste(array $config = [], string $blade = '<x-kit::kontakt-leiste />'): string
    {
        config(['laravelkit.kontakt_leiste' => array_merge(self::VOLL, $config)]);

        return Blade::render($blade);
    }

    /** @return list<\DOMElement> */
    private function links(string $html): array
    {
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8"?><body>'.$html.'</body>');
        $links = [];
        foreach ((new DOMXPath($dom))->query('//nav//a') as $a) {
            $links[] = $a;
        }

        return $links;
    }

    private function css(string $html): string
    {
        preg_match_all('#<style\b[^>]*>(.*?)</style>#s', $html, $m);

        return implode("\n", $m[1]);
    }

    /** Prüfhilfe AK6: Treffer = verbotene Quelle oder Skript. */
    private function hatFremdeQuelle(string $html): bool
    {
        return preg_match('#<script|whatsapp\.com|whatsapp\.net|facebook\.com|facebook\.net|meta\.com#i', $html) === 1;
    }

    /**
     * Prüfhilfe AK8: Regeln als Selektor => Deklarationen (Medienblöcke flach, Kommentare entfernt).
     *
     * @return list<array{0: string, 1: string}>
     */
    private function regeln(string $css): array
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css);
        preg_match_all('/([^{}@;]+)\{([^{}]*)\}/', $css, $m, PREG_SET_ORDER);

        return array_map(static fn (array $r): array => [trim($r[1]), strtolower(preg_replace('/\s+/', '', $r[2]))], $m);
    }

    /** Prüfhilfe AK8: Hat ein Selektor box-sizing:border-box, wo er seine Größe festlegt? */
    private function rahmenInGroesse(string $css, string $selektor): bool
    {
        foreach ($this->regeln($css) as [$sel, $deklaration]) {
            if (in_array($selektor, array_map('trim', explode(',', $sel)), true) && str_contains($deklaration, 'box-sizing:border-box')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prüfhilfe AK10: Farbliterale außerhalb der Variablen-Definition.
     *
     * @return list<string>
     */
    private function farbliterale(string $css): array
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css);
        $css = preg_replace('/:where\(\.kontakt-leiste\)\s*\{[^}]*\}/', '', $css);
        $css = preg_replace('/var\(\s*--[a-z0-9-]+/i', 'var(', $css);
        $treffer = [];
        preg_match_all('/#[0-9a-f]{3,8}\b|\b(?:rgba?|hsla?|hwb|lab|lch|oklab|oklch|color)\(/i', $css, $m);
        array_push($treffer, ...$m[0]);
        $benannt = 'white|black|red|green|blue|gray|grey|silver|yellow|orange|purple|pink|brown|navy|teal|lime|aqua|cyan|magenta|maroon|olive|gold|crimson|tomato|coral|salmon|indigo|violet|whitesmoke|lightgray|lightgrey|darkgray|darkgrey|dimgray|slategray';
        preg_match_all('/(?:^|[;{\s])(?:color|background(?:-color|-image)?|border(?:-[a-z]+)*|outline(?:-color)?|box-shadow|text-shadow|fill|stroke)\s*:([^;}]*)/i', $css, $d);
        foreach ($d[1] as $wert) {
            if (preg_match_all('/\b(?:'.$benannt.')\b/i', $wert, $n)) {
                array_push($treffer, ...$n[0]);
            }
        }

        return $treffer;
    }

    // AK1
    public function test_genau_eine_nav_mit_drei_links_in_reihenfolge(): void
    {
        $html = $this->leiste();

        $this->assertSame(1, substr_count($html, '<nav'));
        $this->assertMatchesRegularExpression('#<nav class="kontakt-leiste" aria-label="Direktkontakt">#', $html);
        $links = $this->links($html);
        $this->assertCount(3, $links);
        $this->assertStringStartsWith('tel:', $links[0]->getAttribute('href'));
        $this->assertStringStartsWith('mailto:', $links[1]->getAttribute('href'));
        $this->assertStringStartsWith('https://wa.me/', $links[2]->getAttribute('href'));
    }

    // AK2
    public function test_telefon_link_mit_e164_und_anzeigeform(): void
    {
        $link = $this->links($this->leiste())[0];

        $this->assertSame('tel:+4943443899378', $link->getAttribute('href'));
        $this->assertStringContainsString('+49 4344 - 389 937 8', preg_replace('/\s+/', ' ', $link->textContent));
    }

    // AK3
    public function test_email_link_mit_adresse_als_text(): void
    {
        $link = $this->links($this->leiste())[1];

        $this->assertSame('mailto:info@example.com', $link->getAttribute('href'));
        $this->assertStringContainsString('info@example.com', $link->textContent);
    }

    // AK4
    public function test_whatsapp_link_mit_kodiertem_text_und_attributen(): void
    {
        $link = $this->links($this->leiste())[2];

        $this->assertSame('https://wa.me/4943443899378?text='.rawurlencode('Hallo, ich habe eine Frage'), $link->getAttribute('href'));
        $this->assertSame('_blank', $link->getAttribute('target'));
        $this->assertSame('noopener noreferrer', $link->getAttribute('rel'));
        $this->assertSame('Per WhatsApp schreiben (öffnet neues Fenster)', $link->getAttribute('aria-label'));
    }

    // AK5
    public function test_nummer_in_jeder_schreibweise_wird_zu_ziffern_und_whatsapp_kann_eigene_nummer_haben(): void
    {
        foreach (['+49 4344 3899378', '+49-4344-3899378', '+4943443899378'] as $nummer) {
            $links = $this->links($this->leiste(['telefon' => $nummer]));
            $this->assertSame('tel:+4943443899378', $links[0]->getAttribute('href'), $nummer);
            $this->assertStringStartsWith('https://wa.me/4943443899378', $links[2]->getAttribute('href'), $nummer);
        }

        $links = $this->links($this->leiste(['whatsapp' => '+49 170 1234567']));
        $this->assertSame('tel:+4943443899378', $links[0]->getAttribute('href'));
        $this->assertStringStartsWith('https://wa.me/491701234567?', $links[2]->getAttribute('href'));
    }

    // AK6
    public function test_keine_fremden_quellen_kein_script_und_svg_symbole(): void
    {
        $html = $this->leiste();

        $this->assertTrue($this->hatFremdeQuelle('<script src="https://static.whatsapp.net/x.js"></script>'), 'Gegenprobe');
        $this->assertFalse($this->hatFremdeQuelle($html));
        $this->assertSame(3, substr_count($html, '<svg'));
        $this->assertSame(3, substr_count($html, 'aria-hidden="true"'));
    }

    // AK7
    public function test_fehlender_kontaktweg_entfaellt_nur_dieser_link(): void
    {
        $this->assertCount(3, $this->links($this->leiste()), 'Gegenprobe');

        // WhatsApp mit eigener Nummer, damit nur der Telefon-Link entfällt
        $ohneTelefon = $this->links($this->leiste(['telefon' => '', 'whatsapp' => '+49 170 1234567']));
        $this->assertCount(2, $ohneTelefon);
        $this->assertStringStartsWith('mailto:', $ohneTelefon[0]->getAttribute('href'));
        $this->assertStringStartsWith('https://wa.me/491701234567', $ohneTelefon[1]->getAttribute('href'));

        $ohneEmail = $this->links($this->leiste(['email' => null]));
        $this->assertCount(2, $ohneEmail);
        $this->assertStringStartsWith('tel:', $ohneEmail[0]->getAttribute('href'));

        $ohneWhatsapp = $this->links($this->leiste(['telefon' => '', 'whatsapp' => '']));
        $this->assertCount(1, $ohneWhatsapp);
        $this->assertStringStartsWith('mailto:', $ohneWhatsapp[0]->getAttribute('href'));
    }

    // AK7
    public function test_ohne_kontaktwege_oder_inaktiv_keine_ausgabe(): void
    {
        $this->assertStringContainsString('<nav', $this->leiste(), 'Gegenprobe');

        $this->assertSame('', trim($this->leiste(['telefon' => '', 'email' => '', 'whatsapp' => null])));
        $this->assertSame('', trim($this->leiste(['aktiv' => false])));
    }

    // AK10
    public function test_css_definiert_die_variablen_mit_standardwerten(): void
    {
        $css = $this->css($this->leiste());

        foreach (['bg', 'text', 'rand', 'font', 'top', 'z'] as $name) {
            $this->assertMatchesRegularExpression('/--kontakt-leiste-'.$name.'\s*:/', $css, $name);
        }
        $this->assertStringContainsString('var(--kontakt-leiste-bg', $css);
    }

    // AK8 (CSS-Inhalt; echte Messung im Browser macht der Moderator)
    public function test_faehnchen_und_fahne_enthalten_den_rahmen_in_52_mal_52_ohne_projekt_reset(): void
    {
        $css = $this->css($this->leiste());

        $this->assertFalse($this->rahmenInGroesse('.kontakt-leiste a{width:52px;height:52px;border:1px solid var(--x)}', '.kontakt-leiste a'), 'Gegenprobe');
        $this->assertTrue($this->rahmenInGroesse('.kontakt-leiste a{box-sizing:border-box;width:52px}', '.kontakt-leiste a'), 'Gegenprobe');
        $this->assertTrue($this->rahmenInGroesse($css, '.kontakt-leiste a'), 'Fähnchen ohne box-sizing:border-box: Rahmen kommt zu 52 px dazu');
        $this->assertTrue($this->rahmenInGroesse($css, '.kontakt-leiste .lang'), 'Fahne ohne box-sizing:border-box: Rahmen kommt zur Höhe 52 px dazu');
    }

    // AK10
    public function test_css_hat_ausserhalb_der_variablen_keine_farbliterale(): void
    {
        $this->assertNotSame([], $this->farbliterale('.x{box-shadow:0 0 4px rgba(0,0,0,.2)}'), 'Gegenprobe rgba');
        $this->assertNotSame([], $this->farbliterale('.x{color:#fff}'), 'Gegenprobe hex');
        $this->assertNotSame([], $this->farbliterale('.x{border:1px solid white}'), 'Gegenprobe benannt');
        $this->assertNotSame([], $this->farbliterale('.x{color:var(--a,#fff)}'), 'Gegenprobe Rückfallwert');
        $this->assertSame([], $this->farbliterale(':where(.kontakt-leiste){--kontakt-leiste-bg:#1f2937}.x{color:var(--kontakt-leiste-bg);outline:2px solid var(--a)}'), 'Gegenprobe sauber');

        $this->assertSame([], $this->farbliterale($this->css($this->leiste())));
    }

    // AK10
    public function test_standardfarben_text_auf_hintergrund_haben_kontrast_mindestens_4_5_zu_1(): void
    {
        $this->assertEqualsWithDelta(21.0, $this->kontrast('#000', '#fff'), 0.01, 'Gegenprobe schwarz auf weiss');
        $this->assertEqualsWithDelta(4.48, $this->kontrast('#777', '#fff'), 0.01, 'Gegenprobe #777 auf weiss');
        $this->assertLessThan(4.5, $this->kontrast('#777', '#fff'), 'Gegenprobe: schlechtes Paar wird erkannt');

        $css = $this->css($this->leiste());
        $this->assertSame(1, preg_match('/:where\(\.kontakt-leiste\)\s*\{([^}]*)\}/', $css, $block));
        $this->assertSame(1, preg_match('/--kontakt-leiste-bg\s*:\s*(#[0-9a-f]{3}|#[0-9a-f]{6})\s*[;}]/i', $block[1], $bg), 'bg als Hex');
        $this->assertSame(1, preg_match('/--kontakt-leiste-text\s*:\s*(#[0-9a-f]{3}|#[0-9a-f]{6})\s*[;}]/i', $block[1].';', $text), 'text als Hex');

        $this->assertGreaterThanOrEqual(4.5, $this->kontrast($text[1], $bg[1]), 'Kontrast '.$text[1].' auf '.$bg[1]);
    }

    /** Prüfhilfe AK10: WCAG-Kontrastverhältnis zweier Hex-Farben (#rgb/#rrggbb). */
    private function kontrast(string $a, string $b): float
    {
        $luminanz = static function (string $hex): float {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $kanal = array_map(static function (string $c): float {
                $v = hexdec($c) / 255;

                return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
            }, str_split($hex, 2));

            return 0.2126 * $kanal[0] + 0.7152 * $kanal[1] + 0.0722 * $kanal[2];
        };
        $la = $luminanz($a);
        $lb = $luminanz($b);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    // AK11
    public function test_css_kommt_mit_der_komponente_ohne_framework_klassen(): void
    {
        $html = $this->leiste();

        $this->assertSame(1, substr_count($html, '<style'));
        $this->assertStringNotContainsString('<link', $html);
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8"?><body>'.$html.'</body>');
        foreach ((new DOMXPath($dom))->query('//nav//*[@class]|//nav[@class]') as $el) {
            foreach (preg_split('/\s+/', trim($el->getAttribute('class'))) as $klasse) {
                $this->assertDoesNotMatchRegularExpression('/^(btn|d-|flex|fixed|absolute|bg-|text-|p[xy]?-|m[xy]?-)/', $klasse);
            }
        }
    }

    // AK9 (nur der CSS-Inhalt; Darstellung prüft der Moderator im Browser)
    public function test_css_hat_medienabfrage_768_und_body_innenabstand_unten(): void
    {
        $css = $this->css($this->leiste());

        $this->assertMatchesRegularExpression('/@media[^{]*768px/', $css);
        $this->assertMatchesRegularExpression('/body\s*\{[^}]*padding-bottom/', $css);
        $this->assertStringContainsString('env(safe-area-inset-bottom', $css);
    }

    // Randfall 1
    public function test_druckansicht_blendet_leiste_aus(): void
    {
        $this->assertMatchesRegularExpression('/@media\s+print\s*\{[^}]*\.kontakt-leiste[^}]*display\s*:\s*none/s', $this->css($this->leiste()));
    }

    // Randfall 2
    public function test_reduzierte_bewegung_ohne_animation(): void
    {
        $this->assertMatchesRegularExpression('/@media\s*\(prefers-reduced-motion:\s*reduce\)\s*\{[^@]*(transition|animation)/s', $this->css($this->leiste()));
    }

    // Randfall 3
    public function test_fokus_ist_sichtbar_und_links_sind_per_tab_erreichbar(): void
    {
        $html = $this->leiste();

        $this->assertStringContainsString(':focus-visible', $this->css($html));
        foreach ($this->links($html) as $link) {
            $this->assertNotSame('', $link->getAttribute('href'));
            $this->assertNotSame('-1', $link->getAttribute('tabindex'));
        }
    }

    // Randfall 4
    public function test_zweimal_eingebunden_css_nur_einmal(): void
    {
        $html = $this->leiste([], '<x-kit::kontakt-leiste /><x-kit::kontakt-leiste />');

        $this->assertSame(2, substr_count($html, '<nav'));
        $this->assertSame(1, substr_count($html, '<style'));
        $this->assertSame(1, substr_count($html, '--kontakt-leiste-bg:'));
    }

    // Randfall 5
    public function test_sonderzeichen_im_whatsapp_text_sind_kodiert(): void
    {
        $href = $this->links($this->leiste(['whatsapp_text' => 'Grüße & Fragen? #eins']))[2]->getAttribute('href');

        $this->assertStringNotContainsString(' ', $href);
        $this->assertSame('https://wa.me/4943443899378?text='.rawurlencode('Grüße & Fragen? #eins'), $href);
        $this->assertSame(1, substr_count($href, '?'));
        $this->assertSame(0, substr_count($href, '#'));
    }
}
