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
    public function test_css_setzt_farben_nur_ueber_variablen(): void
    {
        $css = $this->css($this->leiste());

        foreach (['bg', 'text', 'rand', 'font', 'top', 'z'] as $name) {
            $this->assertMatchesRegularExpression('/--kontakt-leiste-'.$name.'\s*:/', $css, $name);
        }
        $this->assertStringContainsString('var(--kontakt-leiste-bg', $css);
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
