<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use ITHilbert\LaravelKit\Kit\KitFeld;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * kit-blade-formularfelder (#921), Aufgabe T1: neue Kit-Components label, input-file, select, button
 * in beiden Themes sowie die app-abhängigen Teile der Hilfsklasse KitFeld.
 *
 * API KitFeld (vom Test festgelegt, Ergänzung zu tests/Unit/KitFeldTest.php):
 * - KitFeld::alt(string $name): ?string — old(punkt($name)) als Text; nicht vorhanden oder Array → null.
 * - KitFeld::fehler(string $name): ?string — erste Meldung zu punkt($name) aus view()->shared('errors'); ohne Bag → null.
 */
final class FormularNeuTest extends TestCase
{
    private const ANGRIFF = '"><script>alert(1)</script>';

    /** @return array<string, array{string}> */
    public static function themes(): array
    {
        return ['tailwind' => ['tailwind'], 'bootstrap5' => ['bootstrap5']];
    }

    private function rendern(string $theme, string $blade, array $daten = []): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade, $daten);
    }

    private function xpath(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>');
        libxml_clear_errors();

        return new DOMXPath($dom);
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

    private function anzahl(string $html, string $query): int
    {
        $treffer = $this->xpath($html)->query($query);

        return $treffer === false ? 0 : $treffer->length;
    }

    /** @return list<string> */
    private function klassen(DOMElement $element): array
    {
        return preg_split('/\s+/', trim($element->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
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

    /** @return list<string> Vue-Tags sowie v-/:-Attribute im Markup */
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

    /** @return list<array{string, string, bool}> [Wert, Text, ausgewählt] */
    private function optionen(string $html): array
    {
        $liste = [];
        foreach ($this->xpath($html)->query('//select/option') ?: [] as $option) {
            if ($option instanceof DOMElement) {
                $liste[] = [$option->getAttribute('value'), trim($option->textContent), $option->hasAttribute('selected')];
            }
        }

        return $liste;
    }

    // ================================================================= AK1 label

    // AK1
    public function test_label_tailwind_hat_for_und_klasse_hlabel(): void
    {
        $html = $this->rendern('tailwind', '<x-kit::label for="name">Text</x-kit::label>');

        $label = $this->element($html, '//label');
        $this->assertSame('name', $label->getAttribute('for'));
        $this->assertSame(['hlabel'], $this->klassen($label));
        $this->assertSame('Text', trim($label->textContent));
    }

    // AK1
    public function test_label_bootstrap5_hat_for_und_klasse_form_label(): void
    {
        $html = $this->rendern('bootstrap5', '<x-kit::label for="name">Text</x-kit::label>');

        $label = $this->element($html, '//label');
        $this->assertSame('name', $label->getAttribute('for'));
        $this->assertSame(['form-label'], $this->klassen($label));
        $this->assertSame('Text', trim($label->textContent));
    }

    // AK1
    #[DataProvider('themes')]
    public function test_label_weitere_attribute_landen_am_element(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::label for="name" id="l1" data-hinweis="ja" title="Titel">Text</x-kit::label>');

        $label = $this->element($html, '//label');
        $this->assertSame('l1', $label->getAttribute('id'));
        $this->assertSame('ja', $label->getAttribute('data-hinweis'));
        $this->assertSame('Titel', $label->getAttribute('title'));
        $this->assertSame('name', $label->getAttribute('for'));
    }

    // AK1, AK7
    #[DataProvider('themes')]
    public function test_label_text_wird_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::label for="name">{{ $text }}</x-kit::label>', ['text' => self::ANGRIFF]);

        $label = $this->element($html, '//label');
        $this->assertSame(self::ANGRIFF, trim($label->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK8
    #[DataProvider('themes')]
    public function test_label_ohne_vue_spuren(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::label for="name">Text</x-kit::label>');

        $this->element($html, "//label[@for='name']");
        $this->assertSame([], $this->vueSpuren($html), $html);
    }

    // ============================================================ AK2 input-file

    // AK2
    #[DataProvider('themes')]
    public function test_input_file_erzeugt_dateifeld_mit_id_und_name(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" label="Bild" accept="image/png" multiple />');

        $feld = $this->element($html, "//input[@type='file']");
        $this->assertSame('bild', $feld->getAttribute('id'));
        $this->assertSame('bild', $feld->getAttribute('name'));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_input_file_hat_label_for_auf_das_feld(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" label="Bild" accept="image/png" multiple />');

        $label = $this->element($html, '//label');
        $this->assertSame('bild', $label->getAttribute('for'));
        $this->assertSame('Bild', trim($label->textContent));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_input_file_uebernimmt_accept_multiple_und_required(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" label="Bild" accept="image/png" multiple required />');

        $feld = $this->element($html, "//input[@type='file']");
        $this->assertSame('image/png', $feld->getAttribute('accept'));
        $this->assertTrue($feld->hasAttribute('multiple'));
        $this->assertTrue($feld->hasAttribute('required'));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_input_file_ohne_multiple_und_required_hat_beide_nicht(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" label="Bild" accept="image/png" />');

        $feld = $this->element($html, "//input[@type='file']");
        $this->assertSame('image/png', $feld->getAttribute('accept'));
        $this->assertFalse($feld->hasAttribute('multiple'));
        $this->assertFalse($feld->hasAttribute('required'));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_input_file_wird_nie_aus_old_vorbelegt(string $theme): void
    {
        $this->alteEingaben(['bild' => 'C:\\fake\\bild.png', 'titel' => 'Alt']);

        $html = $this->rendern($theme, '<x-kit::input-text name="titel" /><x-kit::input-file name="bild" label="Bild" />');

        // Gegenprobe: old() greift im selben Rendern
        $this->assertSame('Alt', $this->element($html, "//input[@name='titel']")->getAttribute('value'));
        $this->assertFalse($this->element($html, "//input[@type='file']")->hasAttribute('value'));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_input_file_wird_nie_aus_value_vorbelegt(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" label="Bild" value="bild.png" />');

        $feld = $this->element($html, "//input[@type='file'][@name='bild']");
        $this->assertFalse($feld->hasAttribute('value'));
    }

    // AK2, AK7
    #[DataProvider('themes')]
    public function test_input_file_label_wird_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" :label="$text" />', ['text' => self::ANGRIFF]);

        $this->assertSame(self::ANGRIFF, trim($this->element($html, '//label')->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK8
    #[DataProvider('themes')]
    public function test_input_file_ohne_vue_spuren(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::input-file name="bild" label="Bild" accept="image/png" multiple />');

        $this->element($html, "//input[@type='file']");
        $this->assertSame([], $this->vueSpuren($html), $html);
    }

    // ================================================================ AK3 select

    // AK3
    #[DataProvider('themes')]
    public function test_select_erzeugt_select_mit_id_und_name(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" label="Status" :options="$o" />', ['o' => ['a' => 'Alpha']]);

        $select = $this->element($html, '//select');
        $this->assertSame('status', $select->getAttribute('id'));
        $this->assertSame('status', $select->getAttribute('name'));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_hat_label_for_auf_das_feld(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" label="Status" :options="$o" />', ['o' => ['a' => 'Alpha']]);

        $label = $this->element($html, '//label');
        $this->assertSame('status', $label->getAttribute('for'));
        $this->assertSame('Status', trim($label->textContent));
    }

    /** @return array<string, array{string, string}> */
    public static function optionsFormen(): array
    {
        $faelle = [];
        foreach (['tailwind', 'bootstrap5'] as $theme) {
            foreach (['array cbKey', 'collection cbKey', 'collection objekte', 'json cbKey', 'schluessel text'] as $form) {
                $faelle["{$theme} {$form}"] = [$theme, $form];
            }
        }

        return $faelle;
    }

    private function optionsIn(string $form): mixed
    {
        $liste = [['cbKey' => 'a', 'cbCaption' => 'Alpha'], ['cbKey' => 'b', 'cbCaption' => 'Beta']];

        return match ($form) {
            'array cbKey' => $liste,
            'collection cbKey' => collect($liste),
            'collection objekte' => collect(array_map(static fn (array $e): object => (object) $e, $liste)),
            'json cbKey' => (string) json_encode($liste),
            'schluessel text' => ['a' => 'Alpha', 'b' => 'Beta'],
        };
    }

    // AK3
    #[DataProvider('optionsFormen')]
    public function test_select_nimmt_options_in_jeder_form(string $theme, string $form): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => $this->optionsIn($form)]);

        $this->assertSame([['a', 'Alpha', false], ['b', 'Beta', false]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_schluessel_text_mit_zahlenschluesseln(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => [1 => 'Eins', 2 => 'Zwei']]);

        $this->assertSame([['1', 'Eins', false], ['2', 'Zwei', false]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_placeholder_ergibt_erste_option_mit_leerem_wert(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" placeholder="Bitte wählen" />', ['o' => ['a' => 'Alpha']]);

        $optionen = $this->optionen($html);
        $this->assertCount(2, $optionen);
        $this->assertSame('', $optionen[0][0]);
        $this->assertSame('Bitte wählen', $optionen[0][1]);
        $this->assertSame(['a', 'Alpha'], [$optionen[1][0], $optionen[1][1]]);
    }

    // AK3 (nicht gesetzt)
    #[DataProvider('themes')]
    public function test_select_ohne_placeholder_hat_keine_leere_option(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => ['a' => 'Alpha']]);

        $this->assertSame([['a', 'Alpha', false]], $this->optionen($html));
    }

    // AK3 (leer gesetzt — Annahme: wie nicht gesetzt)
    #[DataProvider('themes')]
    public function test_select_mit_leerem_placeholder_hat_keine_leere_option(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" placeholder="" />', ['o' => ['a' => 'Alpha']]);

        $this->assertSame([['a', 'Alpha', false]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_waehlt_value(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" value="b" />', ['o' => ['a' => 'Alpha', 'b' => 'Beta']]);

        $this->assertSame([['a', 'Alpha', false], ['b', 'Beta', true]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_waehlt_ohne_value_old(string $theme): void
    {
        $this->alteEingaben(['status' => 'b']);

        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => ['a' => 'Alpha', 'b' => 'Beta']]);

        $this->assertSame([['a', 'Alpha', false], ['b', 'Beta', true]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_value_geht_vor_old(string $theme): void
    {
        $this->alteEingaben(['status' => 'b']);

        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" value="a" />', ['o' => ['a' => 'Alpha', 'b' => 'Beta']]);

        $this->assertSame([['a', 'Alpha', true], ['b', 'Beta', false]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_ohne_value_und_old_waehlt_nichts(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => ['a' => 'Alpha', 'b' => 'Beta']]);

        $this->assertSame([['a', 'Alpha', false], ['b', 'Beta', false]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_multiple_waehlt_alle_werte_aus_value_array(string $theme): void
    {
        $html = $this->rendern(
            $theme,
            '<x-kit::select name="tags[]" multiple :options="$o" :value="$v" />',
            ['o' => ['a' => 'Alpha', 'b' => 'Beta', 'c' => 'Gamma'], 'v' => ['a', 'c']],
        );

        $this->assertTrue($this->element($html, '//select')->hasAttribute('multiple'));
        $this->assertSame([['a', 'Alpha', true], ['b', 'Beta', false], ['c', 'Gamma', true]], $this->optionen($html));
    }

    // AK3
    #[DataProvider('themes')]
    public function test_select_multiple_waehlt_alle_werte_aus_old_array(string $theme): void
    {
        $this->alteEingaben(['tags' => ['b', 'c']]);

        $html = $this->rendern(
            $theme,
            '<x-kit::select name="tags[]" multiple :options="$o" />',
            ['o' => ['a' => 'Alpha', 'b' => 'Beta', 'c' => 'Gamma']],
        );

        $this->assertSame([['a', 'Alpha', false], ['b', 'Beta', true], ['c', 'Gamma', true]], $this->optionen($html));
    }

    // AK3, AK7
    #[DataProvider('themes')]
    public function test_select_optionstexte_und_werte_werden_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => [self::ANGRIFF => self::ANGRIFF]]);

        $this->assertSame([[self::ANGRIFF, self::ANGRIFF, false]], $this->optionen($html));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK3, AK7
    #[DataProvider('themes')]
    public function test_select_cbcaption_wird_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :options="$o" />', ['o' => [['cbKey' => 'x', 'cbCaption' => self::ANGRIFF]]]);

        $this->assertSame([['x', self::ANGRIFF, false]], $this->optionen($html));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK3, AK7
    #[DataProvider('themes')]
    public function test_select_label_und_placeholder_werden_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" :label="$t" :placeholder="$t" :options="$o" />', ['t' => self::ANGRIFF, 'o' => ['a' => 'Alpha']]);

        $this->assertSame(self::ANGRIFF, trim($this->element($html, '//label')->textContent));
        $this->assertSame(self::ANGRIFF, $this->optionen($html)[0][1]);
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK8
    #[DataProvider('themes')]
    public function test_select_ohne_vue_spuren(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::select name="status" label="Status" placeholder="Bitte wählen" :options="$o" value="a" />', ['o' => ['a' => 'Alpha']]);

        $this->element($html, '//select');
        $this->assertSame([], $this->vueSpuren($html), $html);
    }

    // ================================================================ AK4 button

    // AK4
    #[DataProvider('themes')]
    public function test_button_standard_ist_type_button_mit_btn_primary(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button>Text</x-kit::button>');

        $button = $this->element($html, '//button');
        $this->assertSame('button', $button->getAttribute('type'));
        $this->assertSame(['btn', 'btn-primary'], $this->klassen($button));
        $this->assertSame('Text', trim($button->textContent));
    }

    // AK4
    #[DataProvider('themes')]
    public function test_button_variant_secondary_aendert_die_klasse(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button variant="secondary">Text</x-kit::button>');

        $button = $this->element($html, '//button');
        $this->assertContains('btn', $this->klassen($button));
        $this->assertContains('btn-secondary', $this->klassen($button));
        $this->assertNotContains('btn-primary', $this->klassen($button));
        $this->assertFalse($button->hasAttribute('variant'));
    }

    // AK4 (leer gesetzt — Annahme: wie nicht gesetzt)
    #[DataProvider('themes')]
    public function test_button_leere_variant_bleibt_btn_primary(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button variant="">Text</x-kit::button>');

        $this->assertSame(['btn', 'btn-primary'], $this->klassen($this->element($html, '//button')));
    }

    // AK4
    #[DataProvider('themes')]
    public function test_button_type_submit_aendert_den_typ(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button type="submit">Text</x-kit::button>');

        $button = $this->element($html, '//button');
        $this->assertSame('submit', $button->getAttribute('type'));
        $this->assertSame(['btn', 'btn-primary'], $this->klassen($button));
    }

    // AK4
    #[DataProvider('themes')]
    public function test_button_weitere_attribute_landen_am_element(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button id="b1" name="aktion" value="los" data-x="y" disabled>Text</x-kit::button>');

        $button = $this->element($html, '//button');
        $this->assertSame('b1', $button->getAttribute('id'));
        $this->assertSame('aktion', $button->getAttribute('name'));
        $this->assertSame('los', $button->getAttribute('value'));
        $this->assertSame('y', $button->getAttribute('data-x'));
        $this->assertTrue($button->hasAttribute('disabled'));
    }

    // AK4 (Annahme: zusätzliche Klassen ergänzen btn/btn-<variant>)
    #[DataProvider('themes')]
    public function test_button_zusaetzliche_klasse_ergaenzt_die_btn_klassen(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button class="w-full">Text</x-kit::button>');

        $klassen = $this->klassen($this->element($html, '//button'));
        $this->assertContains('btn', $klassen);
        $this->assertContains('btn-primary', $klassen);
        $this->assertContains('w-full', $klassen);
    }

    // AK4, AK7
    #[DataProvider('themes')]
    public function test_button_text_aus_variable_wird_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button>{{ $t }}</x-kit::button>', ['t' => self::ANGRIFF]);

        $this->assertSame(self::ANGRIFF, trim($this->element($html, '//button')->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
    }

    // AK8
    #[DataProvider('themes')]
    public function test_button_ohne_vue_spuren(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::button variant="secondary" type="submit">Text</x-kit::button>');

        $this->element($html, '//button');
        $this->assertSame([], $this->vueSpuren($html), $html);
    }

    // AK8
    public function test_die_suche_nach_vue_spuren_erkennt_tags_und_attribute(): void
    {
        $this->assertNotSame([], $this->vueSpuren('<card-main>x</card-main>'));
        $this->assertNotSame([], $this->vueSpuren('<x-kit::label for="a">x</x-kit::label>'));
        $this->assertNotSame([], $this->vueSpuren('<a v-if="x" href="#">x</a>'));
        $this->assertNotSame([], $this->vueSpuren('<a :href="x">x</a>'));
        $this->assertSame([], $this->vueSpuren('<label for="a" aria-describedby="a-error" data-x="1">x</label>'));
    }

    // ===================================================== KitFeld (app-abhängig)

    // AK5
    public function test_kitfeld_alt_liest_old_in_punktschreibweise(): void
    {
        $this->alteEingaben(['adresse' => ['ort' => 'Berlin'], 'name' => 'Max']);

        $this->assertSame('Berlin', KitFeld::alt('adresse[ort]'));
        $this->assertSame('Max', KitFeld::alt('name'));
    }

    // AK5
    public function test_kitfeld_alt_liest_listen_namen_ohne_klammern(): void
    {
        $this->alteEingaben(['tags' => 'eins']);

        $this->assertSame('eins', KitFeld::alt('tags[]'));
    }

    // AK5
    public function test_kitfeld_alt_mit_array_fuer_einzelfeld_ist_leer(): void
    {
        $this->alteEingaben(['name' => ['a', 'b'], 'ort' => 'Berlin']);

        // Gegenprobe: alte Eingaben sind geladen
        $this->assertSame('Berlin', KitFeld::alt('ort'));
        $this->assertNull(KitFeld::alt('name'));
    }

    // AK5
    public function test_kitfeld_alt_ohne_alte_eingabe_ist_null(): void
    {
        $this->alteEingaben(['ort' => 'Berlin']);

        $this->assertSame('Berlin', KitFeld::alt('ort'));
        $this->assertNull(KitFeld::alt('name'));
    }

    // AK5
    public function test_kitfeld_alt_ohne_session_ist_null(): void
    {
        $this->assertNull(KitFeld::alt('name'));
    }

    // Randfall 1
    public function test_kitfeld_alt_null_bleibt_null_als_text(): void
    {
        $this->alteEingaben(['zahl' => 0, 'text' => '0', 'leer' => '']);

        $this->assertSame('0', KitFeld::alt('zahl'));
        $this->assertSame('0', KitFeld::alt('text'));
        $this->assertSame('', KitFeld::alt('leer'));
    }

    // AK6
    public function test_kitfeld_fehler_liefert_erste_meldung_in_punktschreibweise(): void
    {
        $this->fehlerTeilen(['adresse.ort' => ['Ort fehlt.', 'Ort zu kurz.'], 'name' => ['Name fehlt.']]);

        $this->assertSame('Ort fehlt.', KitFeld::fehler('adresse[ort]'));
        $this->assertSame('Name fehlt.', KitFeld::fehler('name'));
    }

    // AK6
    public function test_kitfeld_fehler_ohne_meldung_zum_feld_ist_null(): void
    {
        $this->fehlerTeilen(['name' => ['Name fehlt.']]);

        $this->assertSame('Name fehlt.', KitFeld::fehler('name'));
        $this->assertNull(KitFeld::fehler('ort'));
    }

    // AK6
    public function test_kitfeld_fehler_ohne_geteilten_bag_ist_null(): void
    {
        $this->assertNull(view()->shared('errors'));
        $this->assertNull(KitFeld::fehler('name'));
    }
}
