<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * kit-blade-formularfelder (#921), Aufgabe T2: old()-Vorbelegung (AK5) und Fehleranzeige (AK6) in den
 * Feld-Components, Escaping (AK7), beide Themes (AK8), Randfälle 1–4.
 *
 * Fehler kommen aus dem geteilten $errors-Bag (view()->share), alte Eingaben aus der Session am Request.
 */
final class FormularOldFehlerTest extends TestCase
{
    private const ANGRIFF = '"><script>alert(1)</script>';

    private const THEMES = ['tailwind', 'bootstrap5'];

    /** AK5: Components mit old()-Vorbelegung → Beispielwert aus old() und ausdrücklicher value */
    private const MIT_OLD = [
        'input-text' => ['Alt Wert', 'Explizit'],
        'input-email' => ['alt@x.de', 'explizit@x.de'],
        'input-date' => ['2026-03-04', '2025-01-01'],
        'input-number' => ['42', '7'],
        'input-euro' => ['12.5', '3.25'],
        'text-area' => ['Alt Text', 'Explizit Text'],
        'combobox' => ['b', 'a'],
        'select' => ['b', 'a'],
    ];

    /** AK6: Components mit Fehleranzeige */
    private const MIT_FEHLER = [
        'input-text', 'input-email', 'input-password', 'input-date', 'input-number', 'input-euro',
        'input-file', 'input-file-img', 'text-area', 'combobox', 'select',
    ];

    /** Components mit Attribut label je Theme (Randfall 2, Randfall 3) */
    private const MIT_LABEL = [
        'tailwind' => ['input-text', 'input-password', 'input-file', 'select'],
        'bootstrap5' => ['input-file', 'select'],
    ];

    // ------------------------------------------------------------ Provider

    /** @return array<string, array{string, string}> */
    public static function oldKomponenten(): array
    {
        $faelle = [];
        foreach (self::THEMES as $theme) {
            foreach (array_keys(self::MIT_OLD) as $k) {
                $faelle["{$theme} {$k}"] = [$theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string, string}> */
    public static function fehlerKomponenten(): array
    {
        $faelle = [];
        foreach (self::THEMES as $theme) {
            foreach (self::MIT_FEHLER as $k) {
                $faelle["{$theme} {$k}"] = [$theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string, string}> */
    public static function fehlerKomponentenBootstrap(): array
    {
        return array_filter(self::fehlerKomponenten(), static fn (array $f): bool => $f[0] === 'bootstrap5');
    }

    /** @return array<string, array{string, string}> */
    public static function labelKomponenten(): array
    {
        $faelle = [];
        foreach (self::MIT_LABEL as $theme => $liste) {
            foreach ($liste as $k) {
                $faelle["{$theme} {$k}"] = [$theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string}> */
    public static function nieVorbelegt(): array
    {
        $faelle = [];
        foreach (self::THEMES as $theme) {
            foreach (['input-password', 'input-file', 'input-file-img'] as $k) {
                $faelle["{$theme} {$k}"] = [$theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string}> */
    public static function themes(): array
    {
        return ['tailwind' => ['tailwind'], 'bootstrap5' => ['bootstrap5']];
    }

    // ------------------------------------------------------------- Hilfen

    /** @return array<string, mixed> */
    private function optionen(string $k): array
    {
        $liste = [['a', 'A'], ['b', 'B'], ['0', 'Null'], [self::ANGRIFF, 'Angriff']];

        if ($k === 'combobox') {
            return ['o' => array_map(static fn (array $e): array => ['cbKey' => $e[0], 'cbCaption' => $e[1]], $liste)];
        }
        if ($k === 'select') {
            $o = [];
            foreach ($liste as [$wert, $text]) {
                $o[$wert] = $text;
            }

            return ['o' => $o];
        }

        return [];
    }

    private function aufruf(string $k, string $name, string $extra = ''): string
    {
        $options = in_array($k, ['combobox', 'select'], true) ? ':options="$o" ' : '';

        return "<x-kit::{$k} name=\"{$name}\" {$options}{$extra} />";
    }

    private function feldQuery(string $k, string $name): string
    {
        $tag = match ($k) {
            'text-area' => 'textarea',
            'combobox', 'select' => 'select',
            default => 'input',
        };

        return "//{$tag}[@name='{$name}']";
    }

    private function rendern(string $theme, string $blade, array $daten = []): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade, $daten);
    }

    private function feldRendern(string $theme, string $k, string $name, string $extra = '', string $davor = ''): string
    {
        return $this->rendern($theme, $davor.$this->aufruf($k, $name, $extra), $this->optionen($k));
    }

    private function xpath(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>');
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function anzahl(string $html, string $query): int
    {
        $treffer = $this->xpath($html)->query($query);

        return $treffer === false ? 0 : $treffer->length;
    }

    private function element(string $html, string $query): DOMElement
    {
        $treffer = $this->xpath($html)->query($query);
        $this->assertNotFalse($treffer);
        $this->assertSame(1, $treffer->length, "Genau ein Treffer für {$query} erwartet:\n{$html}");
        $element = $treffer->item(0);
        $this->assertInstanceOf(DOMElement::class, $element);

        return $element;
    }

    /** Wert des Feldes: value-Attribut, Inhalt der textarea bzw. Werte der gewählten Optionen; null = kein Wert. */
    private function wert(string $html, string $k, string $name): ?string
    {
        $feld = $this->element($html, $this->feldQuery($k, $name));

        if ($k === 'text-area') {
            return $feld->textContent;
        }
        if (in_array($k, ['combobox', 'select'], true)) {
            $gewaehlt = [];
            foreach ($this->xpath($html)->query($this->feldQuery($k, $name).'/option[@selected]') ?: [] as $o) {
                if ($o instanceof DOMElement) {
                    $gewaehlt[] = $o->getAttribute('value');
                }
            }

            return $gewaehlt === [] ? null : implode('|', $gewaehlt);
        }

        return $feld->hasAttribute('value') ? $feld->getAttribute('value') : null;
    }

    /** @return list<string> */
    private function klassen(DOMElement $element): array
    {
        return preg_split('/\s+/', trim($element->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /** @return list<string> */
    private function tokens(DOMElement $element, string $attribut): array
    {
        return preg_split('/\s+/', trim($element->getAttribute($attribut)), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    /** @param array<string, mixed> $alt */
    private function alteEingaben(array $alt): void
    {
        $session = $this->app['session']->driver();
        $session->put('_old_input', $alt);
        $this->app['request']->setLaravelSession($session);
    }

    /** @param array<string, list<string>> $meldungen */
    private function fehlerTeilen(array $meldungen): void
    {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag($meldungen)));
    }

    private function kontrolle(): string
    {
        return '<x-kit::input-text name="kontrolle" />';
    }

    /** @return list<string> */
    private function vueSpuren(string $html): array
    {
        $spuren = [];
        if (preg_match_all('/<[a-z][a-z0-9]*-[a-z0-9-]*[\s>\/]/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }
        if (preg_match_all('/<x-[a-z0-9:.\-]+/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }
        if (preg_match_all('/\sv-[\w:.\-]*(?=[=\s>\/])/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }
        if (preg_match_all('/\s:[\w.\-]+(?=[=\s>\/])/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }

        return $spuren;
    }

    // ================================================================ AK5

    // AK5
    #[DataProvider('oldKomponenten')]
    public function test_old_belegt_den_wert_ohne_value_vor(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => self::MIT_OLD[$k][0]]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(self::MIT_OLD[$k][0], $this->wert($html, $k, 'feld'));
    }

    // AK5
    #[DataProvider('oldKomponenten')]
    public function test_ausdruecklich_uebergebenes_value_hat_vorrang_vor_old(string $theme, string $k): void
    {
        [$alt, $explizit] = self::MIT_OLD[$k];
        $this->alteEingaben(['feld' => $alt]);

        $html = $this->feldRendern($theme, $k, 'feld', "value=\"{$explizit}\"");

        $this->assertSame($explizit, $this->wert($html, $k, 'feld'));
    }

    // AK5 (value leer übergeben — Annahme: gilt als ausdrücklich übergeben)
    #[DataProvider('oldKomponenten')]
    public function test_leer_uebergebenes_value_hat_vorrang_vor_old(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => self::MIT_OLD[$k][0], 'kontrolle' => 'Alt']);

        $html = $this->feldRendern($theme, $k, 'feld', 'value=""', $this->kontrolle());

        // Gegenprobe: old() greift im selben Rendern
        $this->assertSame('Alt', $this->wert($html, 'input-text', 'kontrolle'));
        $this->assertContains($this->wert($html, $k, 'feld'), [null, '']);
    }

    // AK5
    public function test_text_area_slot_hat_vorrang_vor_old_tailwind(): void
    {
        $this->alteEingaben(['bio' => 'Alt Text']);

        $html = $this->rendern('tailwind', '<x-kit::text-area name="bio">Slot Text</x-kit::text-area>');

        $this->assertSame('Slot Text', $this->wert($html, 'text-area', 'bio'));
    }

    // AK5
    public function test_text_area_slot_hat_vorrang_vor_old_bootstrap5(): void
    {
        $this->alteEingaben(['bio' => 'Alt Text']);

        $html = $this->rendern('bootstrap5', '<x-kit::text-area name="bio">Slot Text</x-kit::text-area>');

        $this->assertSame('Slot Text', $this->wert($html, 'text-area', 'bio'));
    }

    // AK5
    #[DataProvider('oldKomponenten')]
    public function test_feldname_mit_einer_ebene_liest_old_in_punktschreibweise(string $theme, string $k): void
    {
        $this->alteEingaben(['adresse' => ['ort' => self::MIT_OLD[$k][0]]]);

        $html = $this->feldRendern($theme, $k, 'adresse[ort]');

        $this->assertSame(self::MIT_OLD[$k][0], $this->wert($html, $k, 'adresse[ort]'));
    }

    // AK5
    #[DataProvider('oldKomponenten')]
    public function test_feldname_als_liste_liest_old_ohne_klammern(string $theme, string $k): void
    {
        $this->alteEingaben(['tags' => self::MIT_OLD[$k][0]]);

        $html = $this->feldRendern($theme, $k, 'tags[]');

        $this->assertSame(self::MIT_OLD[$k][0], $this->wert($html, $k, 'tags[]'));
    }

    // AK5
    #[DataProvider('oldKomponenten')]
    public function test_array_aus_old_fuer_einzelfeld_bleibt_leer_ohne_fehler(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => ['a', 'b'], 'kontrolle' => 'Alt']);

        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        // Gegenprobe: old() greift im selben Rendern
        $this->assertSame('Alt', $this->wert($html, 'input-text', 'kontrolle'));
        $this->assertContains($this->wert($html, $k, 'feld'), [null, '']);
    }

    // AK5
    #[DataProvider('oldKomponenten')]
    public function test_array_aus_old_ueber_echte_anfrage_ergibt_kein_500(string $theme, string $k): void
    {
        config(['laravelkit.theme' => $theme]);
        $blade = $this->kontrolle().$this->aufruf($k, 'feld');
        $daten = $this->optionen($k);
        Route::middleware('web')->get('/_kit/formular', fn () => Blade::render($blade, $daten));

        $html = (string) $this->withSession(['_old_input' => ['feld' => ['a', 'b'], 'kontrolle' => 'Alt']])
            ->get('/_kit/formular')->assertOk()->getContent();

        $this->assertSame('Alt', $this->wert($html, 'input-text', 'kontrolle'));
        $this->assertContains($this->wert($html, $k, 'feld'), [null, '']);
    }

    // AK5
    #[DataProvider('nieVorbelegt')]
    public function test_passwort_und_dateifelder_werden_nie_aus_old_vorbelegt(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => 'GEHEIM_PW_123', 'kontrolle' => 'Alt']);

        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        // Gegenprobe: old() greift im selben Rendern
        $this->assertSame('Alt', $this->wert($html, 'input-text', 'kontrolle'));
        $this->assertNull($this->wert($html, $k, 'feld'));
        $this->assertStringNotContainsString('GEHEIM_PW_123', $html);
    }

    // AK5, AK6
    #[DataProvider('themes')]
    public function test_echte_anfrage_mit_web_middleware_zeigt_old_und_fehler(string $theme): void
    {
        config(['laravelkit.theme' => $theme]);
        Route::middleware('web')->get('/_kit/formular', fn () => Blade::render('<x-kit::input-text name="vorname" />'));
        $fehler = (new ViewErrorBag)->put('default', new MessageBag(['vorname' => ['Vorname ist zu kurz.']]));

        $html = (string) $this->withSession(['_old_input' => ['vorname' => 'Al'], 'errors' => $fehler])
            ->get('/_kit/formular')->assertOk()->getContent();

        $this->assertSame('Al', $this->wert($html, 'input-text', 'vorname'));
        $this->assertSame('Vorname ist zu kurz.', trim($this->element($html, "//*[@id='vorname-error']")->textContent));
    }

    // ============================================================ Randfall 1

    // Randfall 1
    #[DataProvider('oldKomponenten')]
    public function test_old_und_value_leer_ergibt_leeres_feld(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => '', 'kontrolle' => 'Alt']);

        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        $this->assertSame('Alt', $this->wert($html, 'input-text', 'kontrolle'));
        $this->assertContains($this->wert($html, $k, 'feld'), [null, '']);
    }

    // Randfall 1
    #[DataProvider('oldKomponenten')]
    public function test_ohne_old_und_value_ist_das_feld_leer(string $theme, string $k): void
    {
        $this->alteEingaben(['kontrolle' => 'Alt']);

        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        $this->assertSame('Alt', $this->wert($html, 'input-text', 'kontrolle'));
        $this->assertContains($this->wert($html, $k, 'feld'), [null, '']);
    }

    // Randfall 1
    #[DataProvider('oldKomponenten')]
    public function test_old_zahl_null_bleibt_null(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => 0]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame('0', $this->wert($html, $k, 'feld'));
    }

    // Randfall 1
    #[DataProvider('oldKomponenten')]
    public function test_old_text_null_bleibt_null(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => '0']);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame('0', $this->wert($html, $k, 'feld'));
    }

    // ================================================================ AK7

    // AK7
    #[DataProvider('oldKomponenten')]
    public function test_wert_aus_old_wird_escaped(string $theme, string $k): void
    {
        $this->alteEingaben(['feld' => self::ANGRIFF]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(self::ANGRIFF, $this->wert($html, $k, 'feld'));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK7
    #[DataProvider('oldKomponenten')]
    public function test_wert_aus_value_wird_escaped(string $theme, string $k): void
    {
        $html = $this->rendern($theme, $this->aufruf($k, 'feld', ':value="$v"'), [...$this->optionen($k), 'v' => self::ANGRIFF]);

        $this->assertSame(self::ANGRIFF, $this->wert($html, $k, 'feld'));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK7
    #[DataProvider('fehlerKomponenten')]
    public function test_fehlermeldung_wird_escaped(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => [self::ANGRIFF]]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(self::ANGRIFF, trim($this->element($html, "//*[@id='feld-error']")->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK7
    #[DataProvider('fehlerKomponenten')]
    public function test_fehlermeldung_aus_error_attribut_wird_escaped(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Bag-Meldung.']]);

        $html = $this->rendern($theme, $this->aufruf($k, 'feld', ':error="$e"'), [...$this->optionen($k), 'e' => self::ANGRIFF]);

        $this->assertSame(self::ANGRIFF, trim($this->element($html, "//*[@id='feld-error']")->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK7
    #[DataProvider('labelKomponenten')]
    public function test_label_wird_escaped(string $theme, string $k): void
    {
        $html = $this->rendern($theme, $this->aufruf($k, 'feld', ':label="$l"'), [...$this->optionen($k), 'l' => self::ANGRIFF]);

        $this->assertSame(self::ANGRIFF, trim($this->element($html, '//label')->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // ================================================================ AK6

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_zeigt_die_erste_meldung_mit_id_feld_error(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.', 'Zweite Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame('Erste Meldung.', trim($this->element($html, "//*[@id='feld-error']")->textContent));
        $this->assertStringNotContainsString('Zweite Meldung.', $html);
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_meldung_steht_unter_dem_feld(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(1, $this->anzahl($html, $this->feldQuery($k, 'feld')."/following::*[@id='feld-error']"), $html);
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_meldung_traegt_die_klasse_des_themes(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $klassen = $this->klassen($this->element($html, "//*[@id='feld-error']"));
        if ($theme === 'tailwind') {
            $this->assertContains('field-error', $klassen);
        } else {
            $this->assertContains('invalid-feedback', $klassen);
            $this->assertContains('d-block', $klassen);
        }
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_feld_bekommt_aria_invalid(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame('true', $this->element($html, $this->feldQuery($k, 'feld'))->getAttribute('aria-invalid'));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_feld_bekommt_aria_describedby_auf_die_meldung(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame('feld-error', $this->element($html, $this->feldQuery($k, 'feld'))->getAttribute('aria-describedby'));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_bestehendes_aria_describedby_bleibt_und_fehler_id_wird_angehaengt(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'aria-describedby="hilfe"');

        $this->assertSame(['hilfe', 'feld-error'], $this->tokens($this->element($html, $this->feldQuery($k, 'feld')), 'aria-describedby'));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_bestehendes_aria_describedby_bleibt_ohne_fehler(string $theme, string $k): void
    {
        $this->fehlerTeilen(['kontrolle' => ['Kontrolle fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'aria-describedby="hilfe"', $this->kontrolle());

        // Gegenprobe: der geteilte Bag wirkt im selben Rendern
        $this->assertSame('kontrolle-error', $this->element($html, "//input[@name='kontrolle']")->getAttribute('aria-describedby'));
        $this->assertSame('hilfe', $this->element($html, $this->feldQuery($k, 'feld'))->getAttribute('aria-describedby'));
    }

    // AK6
    #[DataProvider('fehlerKomponentenBootstrap')]
    public function test_bootstrap_feld_bekommt_klasse_is_invalid(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertContains('is-invalid', $this->klassen($this->element($html, $this->feldQuery($k, 'feld'))));
    }

    // AK6
    #[DataProvider('fehlerKomponentenBootstrap')]
    public function test_bootstrap_feld_ohne_fehler_hat_kein_is_invalid(string $theme, string $k): void
    {
        $this->fehlerTeilen(['kontrolle' => ['Kontrolle fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        $this->assertContains('is-invalid', $this->klassen($this->element($html, "//input[@name='kontrolle']")));
        $this->assertNotContains('is-invalid', $this->klassen($this->element($html, $this->feldQuery($k, 'feld'))));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_ohne_fehler_zum_feld_erscheint_nichts_und_attribute_fehlen(string $theme, string $k): void
    {
        $this->fehlerTeilen(['kontrolle' => ['Kontrolle fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        // Gegenprobe: der geteilte Bag wirkt im selben Rendern
        $this->assertSame(1, $this->anzahl($html, "//*[@id='kontrolle-error']"));
        $this->assertSame(0, $this->anzahl($html, "//*[@id='feld-error']"));
        $feld = $this->element($html, $this->feldQuery($k, 'feld'));
        $this->assertFalse($feld->hasAttribute('aria-invalid'));
        $this->assertFalse($feld->hasAttribute('aria-describedby'));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_leerer_bag_zeigt_nichts_und_attribute_fehlen(string $theme, string $k): void
    {
        $this->fehlerTeilen([]);

        $leer = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(0, $this->anzahl($leer, "//*[@id='feld-error']"));
        $feld = $this->element($leer, $this->feldQuery($k, 'feld'));
        $this->assertFalse($feld->hasAttribute('aria-invalid'));
        $this->assertFalse($feld->hasAttribute('aria-describedby'));

        // Gegenprobe: derselbe Aufruf mit Meldung zeigt sie
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);
        $this->assertSame(1, $this->anzahl($this->feldRendern($theme, $k, 'feld'), "//*[@id='feld-error']"));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_ohne_errors_im_view_erscheint_nichts_und_attribute_fehlen(string $theme, string $k): void
    {
        $ohne = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(0, $this->anzahl($ohne, "//*[@id='feld-error']"));
        $feld = $this->element($ohne, $this->feldQuery($k, 'feld'));
        $this->assertFalse($feld->hasAttribute('aria-invalid'));
        $this->assertFalse($feld->hasAttribute('aria-describedby'));

        // Gegenprobe: derselbe Aufruf mit geteiltem Bag zeigt die Meldung
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);
        $this->assertSame(1, $this->anzahl($this->feldRendern($theme, $k, 'feld'), "//*[@id='feld-error']"));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_error_attribut_ueberschreibt_die_meldung_aus_dem_bag(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Bag-Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'error="Eigene Meldung."');

        $this->assertSame('Eigene Meldung.', trim($this->element($html, "//*[@id='feld-error']")->textContent));
        $this->assertStringNotContainsString('Bag-Meldung.', $html);
        $this->assertFalse($this->element($html, $this->feldQuery($k, 'feld'))->hasAttribute('error'));
    }

    // AK6 (error leer gesetzt — Annahme: wie nicht gesetzt, die Meldung aus dem Bag gilt)
    #[DataProvider('fehlerKomponenten')]
    public function test_leeres_error_attribut_laesst_die_meldung_aus_dem_bag(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Bag-Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'error=""');

        $this->assertSame('Bag-Meldung.', trim($this->element($html, "//*[@id='feld-error']")->textContent));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_fehler_zu_feldname_mit_einer_ebene_in_punktschreibweise(string $theme, string $k): void
    {
        $this->fehlerTeilen(['adresse.ort' => ['Ort fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'adresse[ort]');

        $this->assertSame('Ort fehlt.', trim($this->element($html, "//*[@id='adresse_ort-error']")->textContent));
        $this->assertSame('adresse_ort-error', $this->element($html, $this->feldQuery($k, 'adresse[ort]'))->getAttribute('aria-describedby'));
    }

    // AK6
    #[DataProvider('fehlerKomponenten')]
    public function test_fehler_zu_feldname_als_liste_ohne_klammern(string $theme, string $k): void
    {
        $this->fehlerTeilen(['tags' => ['Tags fehlen.']]);

        $html = $this->feldRendern($theme, $k, 'tags[]');

        $this->assertSame('Tags fehlen.', trim($this->element($html, "//*[@id='tags-error']")->textContent));
        $this->assertSame('tags-error', $this->element($html, $this->feldQuery($k, 'tags[]'))->getAttribute('aria-describedby'));
    }

    // ============================================================ Randfall 2

    // Randfall 2
    #[DataProvider('fehlerKomponenten')]
    public function test_uebergebene_id_gilt_fuer_feld_fehler_id_und_aria_describedby(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'id="eigene"');

        $feld = $this->element($html, $this->feldQuery($k, 'feld'));
        $this->assertSame('eigene', $feld->getAttribute('id'));
        $this->assertSame('eigene-error', $feld->getAttribute('aria-describedby'));
        $this->assertSame('Erste Meldung.', trim($this->element($html, "//*[@id='eigene-error']")->textContent));
        $this->assertSame(0, $this->anzahl($html, "//*[@id='feld-error']"));
    }

    // Randfall 2
    #[DataProvider('labelKomponenten')]
    public function test_uebergebene_id_gilt_fuer_das_label(string $theme, string $k): void
    {
        $html = $this->feldRendern($theme, $k, 'feld', 'id="eigene" label="Beschriftung"');

        $this->assertSame('eigene', $this->element($html, '//label')->getAttribute('for'));
        $this->assertSame('eigene', $this->element($html, $this->feldQuery($k, 'feld'))->getAttribute('id'));
    }

    // Randfall 2
    #[DataProvider('labelKomponenten')]
    public function test_ohne_id_zeigt_das_label_auf_die_feld_id(string $theme, string $k): void
    {
        $html = $this->feldRendern($theme, $k, 'adresse[ort]', 'label="Beschriftung"');

        $feld = $this->element($html, $this->feldQuery($k, 'adresse[ort]'));
        $this->assertNotSame('', $feld->getAttribute('id'));
        $this->assertSame($feld->getAttribute('id'), $this->element($html, '//label')->getAttribute('for'));
    }

    // ============================================================ Randfall 3

    // Randfall 3
    #[DataProvider('labelKomponenten')]
    public function test_ohne_label_kein_label_element_aber_fehleranzeige(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);

        // Gegenprobe: mit label erscheint das Label
        $this->assertSame(1, $this->anzahl($this->feldRendern($theme, $k, 'feld', 'label="Beschriftung"'), '//label'));

        $html = $this->feldRendern($theme, $k, 'feld');

        $this->assertSame(0, $this->anzahl($html, '//label'));
        $this->assertSame('Erste Meldung.', trim($this->element($html, "//*[@id='feld-error']")->textContent));
    }

    // ================================================================ AK8

    // AK8
    #[DataProvider('fehlerKomponenten')]
    public function test_mit_fehler_und_old_ohne_vue_spuren(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Erste Meldung.']]);
        $this->alteEingaben(['feld' => 'a']);

        $html = $this->feldRendern($theme, $k, 'feld', 'aria-describedby="hilfe"');

        $this->element($html, "//*[@id='feld-error']");
        $this->assertSame([], $this->vueSpuren($html), $html);
    }

    // AK8
    public function test_die_suche_nach_vue_spuren_erkennt_tags_und_attribute(): void
    {
        $this->assertNotSame([], $this->vueSpuren('<input-text name="a"></input-text>'));
        $this->assertNotSame([], $this->vueSpuren('<x-kit::input-text name="a" />'));
        $this->assertNotSame([], $this->vueSpuren('<input v-model="x">'));
        $this->assertNotSame([], $this->vueSpuren('<input :value="x">'));
        $this->assertSame([], $this->vueSpuren('<input aria-invalid="true" aria-describedby="a-error"><p id="a-error">x</p>'));
    }

    // ============================================================ Randfall 4

    /**
     * Markup vor diesem Feature (Stand laravel-kit a48ff4d4), erzeugt mit Blade::render ohne Session und ohne $errors.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function bisherigesMarkup(): array
    {
        return [
            'tailwind input-text' => ['tailwind', '<x-kit::input-text name="vorname" label="Vorname" value="Max" class="w-1/2" required />', <<<'HTML'
                <div class="w-full">
                            <label for="vorname" class="hlabel">Vorname</label>
                        <input type="text" id="vorname" name="vorname" class="input-text w-1/2" value="Max" required="required">
                </div>

                HTML],
            'tailwind input-email' => ['tailwind', '<x-kit::input-email name="mail" value="a@b.de" />', <<<'HTML'
                <input type="email" id="mail" name="mail" class="input-email" value="a@b.de">

                HTML],
            'tailwind input-password' => ['tailwind', '<x-kit::input-password name="pw" label="Passwort" />', <<<'HTML'
                <div class="w-full">
                            <label for="pw" class="hlabel">Passwort</label>
                        <div class="relative flex w-full">
                        <input type="password" id="pw" name="pw" class="input-password pr-12">
                    </div>
                </div>

                HTML],
            'tailwind input-date' => ['tailwind', '<x-kit::input-date name="datum" value="2026-01-02" />', <<<'HTML'
                <input type="date" id="datum" name="datum" class="input-date" value="2026-01-02">

                HTML],
            'tailwind input-number' => ['tailwind', '<x-kit::input-number name="menge" value="3" />', <<<'HTML'
                <input type="number" id="menge" name="menge" class="input-number" value="3">

                HTML],
            'tailwind input-euro' => ['tailwind', '<x-kit::input-euro name="preis" value="9.5" />', <<<'HTML'
                <div class="flex w-full rounded-md shadow-sm">
                    <input type="number" id="preis" name="preis" class="input-euro flex-1 rounded-r-none border-r-0" lang="de-DE" aria-label="Euro" style="-webkit-appearance: none; -moz-appearance: textfield;" value="9.5">
                    <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">
                        &euro;
                    </span>
                </div>

                HTML],
            'tailwind input-file-img' => ['tailwind', '<x-kit::input-file-img name="bild" />', <<<'HTML'
                <input type="file" id="bild" name="bild" class="input-file-img px-3 py-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500" accept="image/png, image/jpeg" />

                HTML],
            'tailwind text-area' => ['tailwind', '<x-kit::text-area name="bio" value="Text" />', <<<'HTML'
                <textarea id="bio" name="bio" class="text-area">Text</textarea>

                HTML],
            'tailwind text-area slot' => ['tailwind', '<x-kit::text-area name="bio">Slot</x-kit::text-area>', <<<'HTML'
                <textarea id="bio" name="bio" class="text-area">Slot</textarea>

                HTML],
            'tailwind combobox' => ['tailwind', '<x-kit::combobox name="rolle" :options="[[\'cbKey\' => 1, \'cbCaption\' => \'Admin\'], [\'cbKey\' => 2, \'cbCaption\' => \'User\']]" value="2" />', "<select class=\"combobox\" name=\"rolle\">\n    \n            <option value=\"1\" >Admin</option>\n            <option value=\"2\" selected>User</option>\n    </select>\n"],
            'bootstrap5 input-text' => ['bootstrap5', '<x-kit::input-text name="vorname" value="Max" class="w-1/2" required />', <<<'HTML'
                <input type="text" id="vorname" name="vorname" class="form-control input-text w-1/2" value="Max" required="required">

                HTML],
            'bootstrap5 input-email' => ['bootstrap5', '<x-kit::input-email name="mail" value="a@b.de" />', <<<'HTML'
                <input class="form-control input-email" type="email" id="mail" name="mail" value="a@b.de">

                HTML],
            'bootstrap5 input-password' => ['bootstrap5', '<x-kit::input-password name="pw" />', <<<'HTML'
                <div class="input-group">
                    <input class="form-control input-password" type="password" id="pw" name="pw">
                    <span class="input-group-text"><span data-feather="key"></span></span>
                </div>

                HTML],
            'bootstrap5 input-date' => ['bootstrap5', '<x-kit::input-date name="datum" value="2026-01-02" />', <<<'HTML'
                <input class="form-control input-date" type="date" id="datum" name="datum" value="2026-01-02">

                HTML],
            'bootstrap5 input-number' => ['bootstrap5', '<x-kit::input-number name="menge" value="3" />', <<<'HTML'
                <input class="form-control input-number" type="number" id="menge" name="menge" step="0.01" value="3">

                HTML],
            'bootstrap5 input-euro' => ['bootstrap5', '<x-kit::input-euro name="preis" value="9.5" />', <<<'HTML'
                <div class="input-group">
                    <input class="form-control input-euro" style="-webkit-appearance: none; -moz-appearance: textfield;" type="number" id="preis" name="preis" step="0.01" lang="de-DE" aria-label="Euro" value="9.5">
                    <span class="input-group-text"> &euro; </span>
                </div>

                HTML],
            'bootstrap5 input-file-img' => ['bootstrap5', '<x-kit::input-file-img name="bild" />', <<<'HTML'
                <input type="file" id="bild" name="bild" class="form-control" accept="image/png, image/jpeg" />

                HTML],
            'bootstrap5 text-area' => ['bootstrap5', '<x-kit::text-area name="bio" value="Text" />', <<<'HTML'
                <textarea class="form-control text-area" id="bio" name="bio" rows="3">Text</textarea>

                HTML],
            'bootstrap5 text-area slot' => ['bootstrap5', '<x-kit::text-area name="bio">Slot</x-kit::text-area>', <<<'HTML'
                <textarea class="form-control text-area" id="bio" name="bio" rows="3">Slot</textarea>

                HTML],
            'bootstrap5 combobox' => ['bootstrap5', '<x-kit::combobox name="rolle" :options="[[\'cbKey\' => 1, \'cbCaption\' => \'Admin\'], [\'cbKey\' => 2, \'cbCaption\' => \'User\']]" value="2" />', "<select class=\"form-control combobox\" name=\"rolle\">\n    \n            <option value=\"1\" >Admin</option>\n            <option value=\"2\" selected>User</option>\n    </select>\n"],
        ];
    }

    // Randfall 4
    #[DataProvider('bisherigesMarkup')]
    public function test_ohne_session_und_ohne_errors_byte_gleich_zum_bisherigen_markup(string $theme, string $blade, string $erwartet): void
    {
        $this->assertSame($erwartet, $this->rendern($theme, $blade));
    }

    // Randfall 4
    #[DataProvider('bisherigesMarkup')]
    public function test_mit_leerem_bag_und_session_ohne_old_byte_gleich_zum_bisherigen_markup(string $theme, string $blade, string $erwartet): void
    {
        $this->fehlerTeilen([]);
        $this->alteEingaben([]);

        $this->assertSame($erwartet, $this->rendern($theme, $blade));
    }
}
