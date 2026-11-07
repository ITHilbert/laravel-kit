<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * kit-blade-formularfelder (#921), Aufgabe T4: Schalter der Fehleranzeige (AK10) — standardmäßig aus,
 * pro Feld über das Attribut `fehler`, für alle Felder über die Config `laravelkit.fehleranzeige`
 * (Env `LARAVELKIT_FEHLERANZEIGE`); Attribut vor Config; `error="…"` aktiviert; old() bleibt aktiv.
 */
final class FehleranzeigeSchalterTest extends TestCase
{
    private const THEMES = ['tailwind', 'bootstrap5'];

    private const ENV = 'LARAVELKIT_FEHLERANZEIGE';

    /** AK6/AK10: alle Feld-Components mit Fehleranzeige */
    private const FELDER = [
        'input-text', 'input-email', 'input-password', 'input-date', 'input-number', 'input-euro',
        'input-file', 'input-file-img', 'text-area', 'combobox', 'select',
    ];

    /** AK5: Components mit old()-Vorbelegung und Beispielwert */
    private const MIT_OLD = [
        'input-text' => 'Alt Wert',
        'input-email' => 'alt@x.de',
        'input-date' => '2026-03-04',
        'input-number' => '42',
        'input-euro' => '12.5',
        'text-area' => 'Alt Text',
        'combobox' => 'b',
        'select' => 'b',
    ];

    protected function tearDown(): void
    {
        $this->envSetzen(null);

        parent::tearDown();
    }

    // ------------------------------------------------------------ Provider

    /** @return array<string, array{string, string}> */
    public static function felder(): array
    {
        $faelle = [];
        foreach (self::THEMES as $theme) {
            foreach (self::FELDER as $k) {
                $faelle["{$theme} {$k}"] = [$theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string, string}> */
    public static function felderBootstrap(): array
    {
        return array_filter(self::felder(), static fn (array $f): bool => $f[0] === 'bootstrap5');
    }

    /**
     * Config-Zustände, in denen die Fehleranzeige aus ist: nicht angefasst (Standard), null, false, leer.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function felderBeiAusgeschalteterConfig(): array
    {
        $faelle = [];
        foreach (['standard', 'null', 'false', 'leer'] as $zustand) {
            foreach (self::felder() as $name => [$theme, $k]) {
                $faelle["{$zustand} {$name}"] = [$zustand, $theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string, string}> */
    public static function oldFelder(): array
    {
        $faelle = [];
        foreach (self::THEMES as $theme) {
            foreach (array_keys(self::MIT_OLD) as $k) {
                $faelle["{$theme} {$k}"] = [$theme, $k];
            }
        }

        return $faelle;
    }

    /** @return array<string, array{string}> */
    public static function aktivierendeAttribute(): array
    {
        return [
            'fehler' => ['fehler'],
            ':fehler="true"' => [':fehler="true"'],
        ];
    }

    /** @return array<string, array{string, string, string}> */
    public static function attributFormenJeFeld(): array
    {
        $faelle = [];
        foreach (['fehler', ':fehler="true"', ':fehler="false"', 'fehler=""'] as $form) {
            foreach (self::felder() as $name => [$theme, $k]) {
                $faelle["{$form} {$name}"] = [$form, $theme, $k];
            }
        }

        return $faelle;
    }

    // ------------------------------------------------------------- Hilfen

    /** @return array<string, mixed> */
    private function optionen(string $k): array
    {
        $liste = [['a', 'A'], ['b', 'B']];

        if ($k === 'combobox') {
            return ['o' => array_map(static fn (array $e): array => ['cbKey' => $e[0], 'cbCaption' => $e[1]], $liste)];
        }
        if ($k === 'select') {
            return ['o' => ['a' => 'A', 'b' => 'B']];
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

    private function feldRendern(string $theme, string $k, string $name = 'feld', string $extra = '', string $davor = ''): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($davor.$this->aufruf($k, $name, $extra), $this->optionen($k));
    }

    private function kontrolle(string $extra = ''): string
    {
        return "<x-kit::input-text name=\"kontrolle\" {$extra} />";
    }

    /** @param array<string, list<string>> $meldungen */
    private function fehlerTeilen(array $meldungen): void
    {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag($meldungen)));
    }

    /** @param array<string, mixed> $alt */
    private function alteEingaben(array $alt): void
    {
        $session = $this->app['session']->driver();
        $session->put('_old_input', $alt);
        $this->app['request']->setLaravelSession($session);
    }

    private function configSetzen(string $zustand): void
    {
        match ($zustand) {
            'standard' => null,
            'null' => config(['laravelkit.fehleranzeige' => null]),
            'false' => config(['laravelkit.fehleranzeige' => false]),
            'leer' => config(['laravelkit.fehleranzeige' => '']),
            'true' => config(['laravelkit.fehleranzeige' => true]),
        };
    }

    private function envSetzen(?string $wert): void
    {
        if ($wert === null) {
            putenv(self::ENV);
            unset($_ENV[self::ENV], $_SERVER[self::ENV]);

            return;
        }

        putenv(self::ENV.'='.$wert);
        $_ENV[self::ENV] = $wert;
        $_SERVER[self::ENV] = $wert;
    }

    /** Wert `fehleranzeige` aus der Config-Datei des Pakets, gelesen mit der aktuellen Umgebung. */
    private function configAusDatei(): mixed
    {
        $config = require dirname(__DIR__, 3).'/src/Config/laravelkit.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('fehleranzeige', $config);

        return $config['fehleranzeige'];
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

    private function meldung(string $html, string $id = 'feld-error'): string
    {
        return trim($this->element($html, "//*[@id='{$id}']")->textContent);
    }

    /** Fehleranzeige am Feld: Element <id>-error vorhanden. */
    private function zeigtFehler(string $html, string $id = 'feld'): bool
    {
        return $this->anzahl($html, "//*[@id='{$id}-error']") > 0;
    }

    /** @return list<string> */
    private function klassen(DOMElement $element): array
    {
        return preg_split('/\s+/', trim($element->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

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

    // ================================================== AK10 Standard aus

    // AK10
    public function test_config_fehleranzeige_ist_standardmaessig_false(): void
    {
        $this->assertFalse(config('laravelkit.fehleranzeige'));
    }

    // AK10
    #[DataProvider('felderBeiAusgeschalteterConfig')]
    public function test_ohne_aktivierung_byte_gleich_zu_einem_render_ohne_bag(string $zustand, string $theme, string $k): void
    {
        $this->configSetzen($zustand);
        $ohneBag = $this->feldRendern($theme, $k, 'feld', 'label="Feld" aria-describedby="hilfe"');

        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);
        $mitBag = $this->feldRendern($theme, $k, 'feld', 'label="Feld" aria-describedby="hilfe"');

        // Gegenprobe: derselbe Aufruf mit fehler zeigt die Meldung aus dem Bag
        $this->assertSame('Feld fehlt.', $this->meldung($this->feldRendern($theme, $k, 'feld', 'label="Feld" aria-describedby="hilfe" fehler')));
        $this->assertSame($ohneBag, $mitBag);
    }

    // AK10
    #[DataProvider('felder')]
    public function test_ohne_aktivierung_kein_fehlerelement(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        // Gegenprobe: mit fehler erscheint die Meldung
        $this->assertSame('Feld fehlt.', $this->meldung($this->feldRendern($theme, $k, 'feld', 'fehler')));

        $html = $this->feldRendern($theme, $k);

        $this->assertSame(0, $this->anzahl($html, "//*[@id='feld-error']"));
        $this->assertStringNotContainsString('Feld fehlt.', $html);
    }

    // AK10
    #[DataProvider('felder')]
    public function test_ohne_aktivierung_kein_aria_invalid_und_kein_aria_describedby(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        // Gegenprobe: mit fehler bekommt das Feld beide Attribute
        $an = $this->element($this->feldRendern($theme, $k, 'feld', 'fehler'), $this->feldQuery($k, 'feld'));
        $this->assertSame('true', $an->getAttribute('aria-invalid'));
        $this->assertSame('feld-error', $an->getAttribute('aria-describedby'));

        $feld = $this->element($this->feldRendern($theme, $k), $this->feldQuery($k, 'feld'));

        $this->assertFalse($feld->hasAttribute('aria-invalid'));
        $this->assertFalse($feld->hasAttribute('aria-describedby'));
    }

    // AK10
    #[DataProvider('felder')]
    public function test_ohne_aktivierung_bleibt_ein_uebergebenes_aria_describedby_unveraendert(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        // Gegenprobe: mit fehler wird die Fehler-id angehängt
        $an = $this->element($this->feldRendern($theme, $k, 'feld', 'aria-describedby="hilfe" fehler'), $this->feldQuery($k, 'feld'));
        $this->assertSame('hilfe feld-error', $an->getAttribute('aria-describedby'));

        $feld = $this->element($this->feldRendern($theme, $k, 'feld', 'aria-describedby="hilfe"'), $this->feldQuery($k, 'feld'));

        $this->assertSame('hilfe', $feld->getAttribute('aria-describedby'));
    }

    // AK10
    #[DataProvider('felderBootstrap')]
    public function test_ohne_aktivierung_kein_is_invalid_im_bootstrap_theme(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        // Gegenprobe: mit fehler trägt das Feld is-invalid
        $an = $this->element($this->feldRendern($theme, $k, 'feld', 'fehler'), $this->feldQuery($k, 'feld'));
        $this->assertContains('is-invalid', $this->klassen($an));

        $feld = $this->element($this->feldRendern($theme, $k), $this->feldQuery($k, 'feld'));

        $this->assertNotContains('is-invalid', $this->klassen($feld));
    }

    // ============================================== AK10 Attribut pro Feld

    // AK10
    #[DataProvider('felder')]
    public function test_attribut_fehler_aktiviert_die_anzeige_nur_fuer_dieses_feld(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.'], 'kontrolle' => ['Kontrolle fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'fehler', $this->kontrolle());

        $this->assertSame('Feld fehlt.', $this->meldung($html));
        $this->assertFalse($this->zeigtFehler($html, 'kontrolle'));
    }

    // AK10
    #[DataProvider('felder')]
    public function test_attribut_fehler_true_aktiviert_die_anzeige(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', ':fehler="true"');

        $this->assertSame('Feld fehlt.', $this->meldung($html));
        $feld = $this->element($html, $this->feldQuery($k, 'feld'));
        $this->assertSame('true', $feld->getAttribute('aria-invalid'));
        $this->assertSame('feld-error', $feld->getAttribute('aria-describedby'));
    }

    // AK10
    #[DataProvider('felderBootstrap')]
    public function test_attribut_fehler_setzt_is_invalid_im_bootstrap_theme(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        $feld = $this->element($this->feldRendern($theme, $k, 'feld', 'fehler'), $this->feldQuery($k, 'feld'));

        $this->assertContains('is-invalid', $this->klassen($feld));
    }

    // AK10 (Attribut geht der Config vor: Config aus, Attribut an)
    #[DataProvider('felder')]
    public function test_attribut_fehler_aktiviert_auch_bei_config_false(string $theme, string $k): void
    {
        config(['laravelkit.fehleranzeige' => false]);
        $this->fehlerTeilen(['feld' => ['Feld fehlt.'], 'kontrolle' => ['Kontrolle fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'fehler', $this->kontrolle());

        // Gegenprobe: das Feld ohne Attribut folgt der Config
        $this->assertFalse($this->zeigtFehler($html, 'kontrolle'));
        $this->assertSame('Feld fehlt.', $this->meldung($html));
    }

    // AK10 (Attribut geht der Config vor: Config an, Attribut aus)
    #[DataProvider('felder')]
    public function test_attribut_fehler_false_schaltet_bei_aktiver_config_aus(string $theme, string $k): void
    {
        config(['laravelkit.fehleranzeige' => true]);
        $this->fehlerTeilen(['feld' => ['Feld fehlt.'], 'kontrolle' => ['Kontrolle fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', ':fehler="false" aria-describedby="hilfe"', $this->kontrolle());

        // Gegenprobe: das Feld ohne Attribut folgt der Config
        $this->assertSame('Kontrolle fehlt.', $this->meldung($html, 'kontrolle-error'));
        $this->assertFalse($this->zeigtFehler($html));
        $feld = $this->element($html, $this->feldQuery($k, 'feld'));
        $this->assertFalse($feld->hasAttribute('aria-invalid'));
        $this->assertSame('hilfe', $feld->getAttribute('aria-describedby'));
        $this->assertNotContains('is-invalid', $this->klassen($feld));
    }

    // AK10 (fehler leer gesetzt — Annahme: wie nicht gesetzt, die Config gilt; analog error="" in AK6)
    #[DataProvider('felder')]
    public function test_leeres_attribut_fehler_folgt_der_config(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        $aus = $this->feldRendern($theme, $k, 'feld', 'fehler=""');
        config(['laravelkit.fehleranzeige' => true]);
        $an = $this->feldRendern($theme, $k, 'feld', 'fehler=""');

        $this->assertFalse($this->zeigtFehler($aus));
        $this->assertSame('Feld fehlt.', $this->meldung($an));
    }

    // ================================================= AK10 Config global

    // AK10
    #[DataProvider('felder')]
    public function test_config_true_aktiviert_die_anzeige_fuer_alle_felder(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.'], 'kontrolle' => ['Kontrolle fehlt.']]);

        // Gegenprobe: ohne Config bleibt die Anzeige aus
        $this->assertFalse($this->zeigtFehler($this->feldRendern($theme, $k)));

        config(['laravelkit.fehleranzeige' => true]);
        $html = $this->feldRendern($theme, $k, 'feld', '', $this->kontrolle());

        $this->assertSame('Feld fehlt.', $this->meldung($html));
        $this->assertSame('Kontrolle fehlt.', $this->meldung($html, 'kontrolle-error'));
        $this->assertSame('true', $this->element($html, $this->feldQuery($k, 'feld'))->getAttribute('aria-invalid'));
    }

    // AK10
    public function test_config_datei_liest_die_env_variable(): void
    {
        $this->envSetzen('true');

        $this->assertTrue($this->configAusDatei());
    }

    // AK10
    public function test_config_datei_ohne_env_variable_ist_false(): void
    {
        $this->envSetzen(null);

        $this->assertFalse($this->configAusDatei());
    }

    // AK10 (Env leer gesetzt → aus)
    #[DataProvider('felder')]
    public function test_leer_gesetzte_env_variable_laesst_die_anzeige_aus(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        // Gegenprobe: die Env-Variable wird gelesen
        $this->envSetzen('true');
        config(['laravelkit.fehleranzeige' => $this->configAusDatei()]);
        $this->assertTrue($this->zeigtFehler($this->feldRendern($theme, $k)));

        $this->envSetzen('');
        config(['laravelkit.fehleranzeige' => $this->configAusDatei()]);

        $this->assertFalse($this->zeigtFehler($this->feldRendern($theme, $k)));
    }

    // AK10
    public function test_env_variable_false_laesst_die_anzeige_aus(): void
    {
        $this->envSetzen('false');

        $this->assertFalse($this->configAusDatei());
    }

    // ========================================== AK10 error-Attribut aktiviert

    // AK10
    #[DataProvider('felder')]
    public function test_error_attribut_aktiviert_die_anzeige_ohne_fehler(string $theme, string $k): void
    {
        $html = $this->feldRendern($theme, $k, 'feld', 'error="Eigene Meldung."');

        $this->assertSame('Eigene Meldung.', $this->meldung($html));
        $feld = $this->element($html, $this->feldQuery($k, 'feld'));
        $this->assertSame('true', $feld->getAttribute('aria-invalid'));
        $this->assertSame('feld-error', $feld->getAttribute('aria-describedby'));
    }

    // AK10
    #[DataProvider('felder')]
    public function test_error_attribut_aktiviert_auch_mit_fehler_im_bag(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Bag-Meldung.']]);

        $html = $this->feldRendern($theme, $k, 'feld', 'error="Eigene Meldung."');

        $this->assertSame('Eigene Meldung.', $this->meldung($html));
        $this->assertStringNotContainsString('Bag-Meldung.', $html);
    }

    // AK10 (error leer gesetzt — wie nicht gesetzt, aktiviert nicht; Annahme analog AK6)
    #[DataProvider('felder')]
    public function test_leeres_error_attribut_aktiviert_die_anzeige_nicht(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Bag-Meldung.']]);

        // Gegenprobe: mit Text aktiviert error
        $this->assertTrue($this->zeigtFehler($this->feldRendern($theme, $k, 'feld', 'error="Eigene Meldung."')));

        $html = $this->feldRendern($theme, $k, 'feld', 'error=""');

        $this->assertFalse($this->zeigtFehler($html));
        $this->assertStringNotContainsString('Bag-Meldung.', $html);
    }

    // ========================================= AK10 fehler nicht im HTML

    // AK10
    #[DataProvider('attributFormenJeFeld')]
    public function test_attribut_fehler_erscheint_nicht_im_html(string $form, string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        $html = $this->feldRendern($theme, $k, 'feld', $form);

        // Gegenprobe: das Feld ist gerendert
        $this->element($html, $this->feldQuery($k, 'feld'));
        $this->assertSame(0, $this->anzahl($html, '//*[@fehler]'), $html);
        $this->assertDoesNotMatchRegularExpression('/\sfehler(=|\s|>|\/)/', $html);
    }

    // AK10
    #[DataProvider('aktivierendeAttribute')]
    public function test_attribut_fehler_erscheint_nicht_am_wrapper_mit_label(string $form): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);

        $html = $this->feldRendern('tailwind', 'input-text', 'feld', 'label="Feld" '.$form);

        $this->assertSame('Feld fehlt.', $this->meldung($html));
        $this->assertSame(0, $this->anzahl($html, '//*[@fehler]'), $html);
    }

    // ================================================ AK10 old() bleibt aktiv

    // AK10
    #[DataProvider('oldFelder')]
    public function test_old_bleibt_bei_ausgeschalteter_fehleranzeige_aktiv(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);
        $this->alteEingaben(['feld' => self::MIT_OLD[$k]]);

        $html = $this->feldRendern($theme, $k);

        // Gegenprobe: die Fehleranzeige ist tatsächlich aus
        $this->assertFalse($this->zeigtFehler($html));
        $this->assertSame(self::MIT_OLD[$k], $this->wert($html, $k, 'feld'));
    }

    // AK10
    #[DataProvider('oldFelder')]
    public function test_old_bleibt_bei_fehler_false_trotz_config_aktiv(string $theme, string $k): void
    {
        config(['laravelkit.fehleranzeige' => true]);
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);
        $this->alteEingaben(['feld' => self::MIT_OLD[$k]]);

        $html = $this->feldRendern($theme, $k, 'feld', ':fehler="false"');

        $this->assertFalse($this->zeigtFehler($html));
        $this->assertSame(self::MIT_OLD[$k], $this->wert($html, $k, 'feld'));
    }

    // AK10
    #[DataProvider('oldFelder')]
    public function test_old_und_fehleranzeige_gemeinsam_mit_attribut_fehler(string $theme, string $k): void
    {
        $this->fehlerTeilen(['feld' => ['Feld fehlt.']]);
        $this->alteEingaben(['feld' => self::MIT_OLD[$k]]);

        $html = $this->feldRendern($theme, $k, 'feld', 'fehler');

        $this->assertSame('Feld fehlt.', $this->meldung($html));
        $this->assertSame(self::MIT_OLD[$k], $this->wert($html, $k, 'feld'));
    }
}
