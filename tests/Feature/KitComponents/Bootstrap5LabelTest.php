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
 * kit-bootstrap5-label (#925), Aufgabe T1: Prop `label` in den acht bootstrap5-Feld-Components.
 */
final class Bootstrap5LabelTest extends TestCase
{
    private const ANGRIFF = '"><script>alert(1)</script>';

    /** Komponente → XPath des Feldes */
    private const FELDER = [
        'input-text' => "//input[@type='text']",
        'input-email' => "//input[@type='email']",
        'input-password' => "//input[@type='password']",
        'input-date' => "//input[@type='date']",
        'input-number' => "//input[@type='number']",
        'input-euro' => "//input[@type='number']",
        'text-area' => '//textarea',
        'combobox' => '//select',
    ];

    /**
     * Ausgabe ohne `label` vor dem Feature (Stand laravel-kit main 1.4.0), je Komponente und Variante.
     * Abgeleitet aus dem Render der Vorlagen vor der Änderung.
     */
    private const REFERENZ = [
        'input-text schlicht' => '<input type="text" id="ort" name="ort" class="form-control input-text">
',
        'input-text fehler' => '<input type="text" id="eigene_id" name="adresse[ort]" class="form-control input-text is-invalid" aria-invalid="true" aria-describedby="eigene_id-error"><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'input-text alt' => '<input type="text" id="ort" name="ort" class="form-control input-text is-invalid" value="b" aria-invalid="true" aria-describedby="ort-error"><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'input-email schlicht' => '<input class="form-control input-email" type="email" id="ort" name="ort">
',
        'input-email fehler' => '<input class="form-control input-email  is-invalid" type="email" id="eigene_id" name="adresse[ort]" aria-invalid="true" aria-describedby="eigene_id-error"><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'input-email alt' => '<input class="form-control input-email  is-invalid" type="email" id="ort" name="ort" value="b" aria-invalid="true" aria-describedby="ort-error"><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'input-password schlicht' => '<div class="input-group">
    <input class="form-control input-password" type="password" id="ort" name="ort">
    <span class="input-group-text"><span data-feather="key"></span></span>
</div>
',
        'input-password fehler' => '<div class="input-group">
    <input class="form-control input-password  is-invalid" type="password" id="eigene_id" name="adresse[ort]" aria-invalid="true" aria-describedby="eigene_id-error">
    <span class="input-group-text"><span data-feather="key"></span></span>
</div><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'input-password alt' => '<div class="input-group">
    <input class="form-control input-password  is-invalid" type="password" id="ort" name="ort" aria-invalid="true" aria-describedby="ort-error">
    <span class="input-group-text"><span data-feather="key"></span></span>
</div><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'input-date schlicht' => '<input class="form-control input-date" type="date" id="ort" name="ort">
',
        'input-date fehler' => '<input class="form-control input-date  is-invalid" type="date" id="eigene_id" name="adresse[ort]" aria-invalid="true" aria-describedby="eigene_id-error"><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'input-date alt' => '<input class="form-control input-date  is-invalid" type="date" id="ort" name="ort" value="b" aria-invalid="true" aria-describedby="ort-error"><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'input-number schlicht' => '<input class="form-control input-number" type="number" id="ort" name="ort" step="0.01">
',
        'input-number fehler' => '<input class="form-control input-number  is-invalid" type="number" id="eigene_id" name="adresse[ort]" step="0.01" aria-invalid="true" aria-describedby="eigene_id-error"><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'input-number alt' => '<input class="form-control input-number  is-invalid" type="number" id="ort" name="ort" step="0.01" value="b" aria-invalid="true" aria-describedby="ort-error"><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'input-euro schlicht' => '<div class="input-group">
    <input class="form-control input-euro" style="-webkit-appearance: none; -moz-appearance: textfield;" type="number" id="ort" name="ort" step="0.01" lang="de-DE" aria-label="Euro">
    <span class="input-group-text"> &euro; </span>
</div>
',
        'input-euro fehler' => '<div class="input-group">
    <input class="form-control input-euro  is-invalid" style="-webkit-appearance: none; -moz-appearance: textfield;" type="number" id="eigene_id" name="adresse[ort]" step="0.01" lang="de-DE" aria-label="Euro" aria-invalid="true" aria-describedby="eigene_id-error">
    <span class="input-group-text"> &euro; </span>
</div><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'input-euro alt' => '<div class="input-group">
    <input class="form-control input-euro  is-invalid" style="-webkit-appearance: none; -moz-appearance: textfield;" type="number" id="ort" name="ort" step="0.01" lang="de-DE" aria-label="Euro" value="b" aria-invalid="true" aria-describedby="ort-error">
    <span class="input-group-text"> &euro; </span>
</div><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'text-area schlicht' => '<textarea class="form-control text-area" id="ort" name="ort" rows="3"></textarea>
',
        'text-area fehler' => '<textarea class="form-control text-area  is-invalid" id="eigene_id" name="adresse[ort]" rows="3" aria-invalid="true" aria-describedby="eigene_id-error"></textarea><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'text-area alt' => '<textarea class="form-control text-area  is-invalid" id="ort" name="ort" rows="3" aria-invalid="true" aria-describedby="ort-error">b</textarea><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
        'combobox schlicht' => '<select class="form-control combobox" name="ort">
    
            <option value="a" >Alpha</option>
            <option value="b" >Beta</option>
    </select>
',
        'combobox fehler' => '<select class="form-control combobox  is-invalid" aria-invalid="true" aria-describedby="eigene_id-error" name="adresse[ort]" id="eigene_id">
    
            <option value="a" >Alpha</option>
            <option value="b" >Beta</option>
    </select><div id="eigene_id-error" class="invalid-feedback d-block">Bitte ausfüllen.</div>

',
        'combobox alt' => '<select class="form-control combobox  is-invalid" aria-invalid="true" aria-describedby="ort-error" name="ort">
    
            <option value="a" >Alpha</option>
            <option value="b" selected>Beta</option>
    </select><div id="ort-error" class="invalid-feedback d-block">Ort fehlt.</div>

',
    ];

    /** @return array<string, array{string}> */
    public static function komponenten(): array
    {
        $liste = [];
        foreach (array_keys(self::FELDER) as $komponente) {
            $liste[$komponente] = [$komponente];
        }

        return $liste;
    }

    /** @return array<string, array{string}> */
    public static function eingabefelder(): array
    {
        return array_filter(self::komponenten(), static fn (array $z): bool => $z[0] !== 'combobox');
    }

    /** @return array<string, array{string, string}> */
    public static function komponentenUndVarianten(): array
    {
        $liste = [];
        foreach (array_keys(self::FELDER) as $komponente) {
            foreach (['schlicht', 'fehler', 'alt'] as $variante) {
                $liste["{$komponente} {$variante}"] = [$komponente, $variante];
            }
        }

        return $liste;
    }

    /**
     * Blade-Aufruf je Komponente und Variante; {L} wird durch die Label-Angabe ersetzt.
     */
    public static function aufruf(string $komponente, string $variante, string $label = ''): string
    {
        $optionen = $komponente === 'combobox' ? ' :options="$optionen"' : '';
        $attribute = match ($variante) {
            'schlicht' => 'name="ort"',
            'fehler' => 'name="adresse[ort]" id="eigene_id" error="Bitte ausfüllen."',
            'alt' => 'name="ort" fehler="true"',
        };

        return "<x-kit::{$komponente} {$attribute}{$optionen}".($label !== '' ? " {$label}" : '').' />';
    }

    /** @return array<string, mixed> */
    public static function daten(): array
    {
        return ['optionen' => [['cbKey' => 'a', 'cbCaption' => 'Alpha'], ['cbKey' => 'b', 'cbCaption' => 'Beta']]];
    }

    private function vorbereiten(string $variante): void
    {
        config(['laravelkit.theme' => 'bootstrap5']);

        if ($variante === 'alt') {
            $session = $this->app['session']->driver();
            $session->put('_old_input', ['ort' => 'b']);
            $this->app['request']->setLaravelSession($session);
            view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['ort' => ['Ort fehlt.']])));
        }
    }

    /** @param array<string, mixed> $daten */
    private function rendern(string $blade, array $daten = []): string
    {
        config(['laravelkit.theme' => 'bootstrap5']);

        return Blade::render($blade, array_merge(self::daten(), $daten));
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

    private function feld(string $komponente, string $html): DOMElement
    {
        return $this->element($html, self::FELDER[$komponente]);
    }

    /** Erstes Element-Geschwister nach $element (Text dazwischen übersprungen). */
    private function naechstesElement(DOMElement $element): ?DOMElement
    {
        $knoten = $element->nextSibling;
        while ($knoten !== null && ! $knoten instanceof DOMElement) {
            $knoten = $knoten->nextSibling;
        }

        return $knoten;
    }

    private function enthaelt(DOMElement $aussen, DOMElement $innen): bool
    {
        for ($k = $innen; $k !== null; $k = $k->parentNode) {
            if ($k->isSameNode($aussen)) {
                return true;
            }
        }

        return false;
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_mit_label_entsteht_genau_ein_label_mit_text(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));

        $label = $this->element($html, '//label');
        $this->assertSame('Ort', trim($label->textContent));
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_label_hat_klasse_form_label(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));

        $label = $this->element($html, '//label');
        $klassen = preg_split('/\s+/', trim($label->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $this->assertContains('form-label', $klassen);
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_label_entspricht_der_component_kit_label(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));
        $feld = $this->feld($komponente, $html);
        $erwartet = $this->rendern('<x-kit::label for="'.$feld->getAttribute('id').'">Ort</x-kit::label>');

        $this->assertNotSame('', $feld->getAttribute('id'));
        $this->assertStringStartsWith(trim($erwartet), ltrim($html));
    }

    // AK1
    #[DataProvider('eingabefelder')]
    public function test_for_entspricht_der_feld_id_aus_dem_namen(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));

        $label = $this->element($html, '//label');
        $feld = $this->feld($komponente, $html);
        $this->assertSame('ort', $feld->getAttribute('id'));
        $this->assertSame('ort', $label->getAttribute('for'));
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_for_entspricht_der_uebergebenen_id(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'fehler', 'label="Ort"'));

        $label = $this->element($html, '//label');
        $feld = $this->feld($komponente, $html);
        $this->assertSame('eigene_id', $feld->getAttribute('id'));
        $this->assertSame('eigene_id', $label->getAttribute('for'));
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_label_steht_unmittelbar_vor_dem_feld(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));

        $this->element($html, '//label');
        $this->feld($komponente, $html);
        $xp = $this->xpath($html);
        $label = $xp->query('//label')->item(0);
        $feld = $xp->query(self::FELDER[$komponente])->item(0);
        $this->assertInstanceOf(DOMElement::class, $label);
        $this->assertInstanceOf(DOMElement::class, $feld);
        $danach = $this->naechstesElement($label);
        $this->assertNotNull($danach, "Nach dem Label folgt kein Element:\n{$html}");
        $this->assertTrue($this->enthaelt($danach, $feld), "Das Element nach dem Label ist nicht das Feld (oder dessen Gruppe):\n{$html}");
    }

    // AK1 (Nicht-Ziel: kein Wrapper um Label und Feld)
    #[DataProvider('komponenten')]
    public function test_label_steht_ohne_wrapper_auf_oberster_ebene(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));

        $this->assertSame(1, $this->anzahl($html, '/html/body/label'), "Label nicht auf oberster Ebene:\n{$html}");
        $this->assertSame(1, $this->anzahl($html, '/html/body/*[1][self::label]'), "Label ist nicht das erste Element:\n{$html}");
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_attribut_label_erscheint_nicht_am_feld(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));

        $feld = $this->feld($komponente, $html);
        $this->assertSame('ort', $feld->getAttribute('name'));
        $this->assertFalse($feld->hasAttribute('label'), "Attribut label am Feld:\n{$html}");
        $this->assertSame(0, $this->anzahl($html, '//*[@label]'), "Attribut label an einem Element:\n{$html}");
    }

    // AK1
    #[DataProvider('komponenten')]
    public function test_mit_label_bleibt_fehleranzeige_erhalten(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'fehler', 'label="Ort"'));

        $feld = $this->feld($komponente, $html);
        $this->assertSame('eigene_id-error', $feld->getAttribute('aria-describedby'));
        $this->assertSame(1, $this->anzahl($html, "//*[@id='eigene_id-error']"));
        $this->assertSame(1, $this->anzahl($html, '//label'));
    }

    // AK2
    #[DataProvider('komponentenUndVarianten')]
    public function test_ohne_label_ist_das_markup_byte_gleich_zum_bisherigen(string $komponente, string $variante): void
    {
        $this->assertArrayHasKey("{$komponente} {$variante}", self::REFERENZ);
        $this->vorbereiten($variante);

        $html = $this->rendern(self::aufruf($komponente, $variante));

        $this->assertSame(self::REFERENZ["{$komponente} {$variante}"], $html);
    }

    // AK2
    #[DataProvider('komponenten')]
    public function test_ohne_label_kein_label_element(string $komponente): void
    {
        $mit = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));
        $this->assertSame(1, $this->anzahl($mit, '//label'), "Gegenprobe: mit label erwartet ein Label:\n{$mit}");

        $ohne = $this->rendern(self::aufruf($komponente, 'schlicht'));
        $this->feld($komponente, $ohne);
        $this->assertSame(0, $this->anzahl($ohne, '//label'));
    }

    // AK3
    #[DataProvider('komponenten')]
    public function test_label_text_wird_escaped(string $komponente): void
    {
        $html = $this->rendern(self::aufruf($komponente, 'schlicht', ':label="$angriff"'), ['angriff' => self::ANGRIFF]);

        $label = $this->element($html, '//label');
        $this->assertSame(self::ANGRIFF, trim($label->textContent));
        $this->assertSame(0, $this->anzahl($html, '//script'));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    // AK4
    public function test_combobox_mit_label_ohne_id_bekommt_id_aus_dem_namen(): void
    {
        $html = $this->rendern(self::aufruf('combobox', 'schlicht', 'label="Ort"'));

        $select = $this->feld('combobox', $html);
        $label = $this->element($html, '//label');
        $this->assertSame('ort', $select->getAttribute('id'));
        $this->assertSame('ort', $label->getAttribute('for'));
    }

    // AK4
    public function test_combobox_ohne_label_ohne_id_hat_keine_id(): void
    {
        $mit = $this->rendern(self::aufruf('combobox', 'schlicht', 'label="Ort"'));
        $this->assertSame('ort', $this->feld('combobox', $mit)->getAttribute('id'), "Gegenprobe: mit label erwartet id:\n{$mit}");

        $ohne = $this->rendern(self::aufruf('combobox', 'schlicht'));
        $select = $this->feld('combobox', $ohne);
        $this->assertSame('ort', $select->getAttribute('name'));
        $this->assertFalse($select->hasAttribute('id'), "Combobox ohne label hat eine id:\n{$ohne}");
    }

    // AK4
    public function test_combobox_ohne_label_ist_byte_gleich_zum_bisherigen(): void
    {
        $html = $this->rendern(self::aufruf('combobox', 'schlicht'));

        $this->assertArrayHasKey('combobox schlicht', self::REFERENZ);
        $this->assertSame(self::REFERENZ['combobox schlicht'], $html);
    }

    // Randfall 1
    #[DataProvider('komponenten')]
    public function test_leeres_label_erzeugt_kein_label_element(string $komponente): void
    {
        $mit = $this->rendern(self::aufruf($komponente, 'schlicht', 'label="Ort"'));
        $this->assertSame(1, $this->anzahl($mit, '//label'), "Gegenprobe: mit label erwartet ein Label:\n{$mit}");

        $leer = $this->rendern(self::aufruf($komponente, 'schlicht', 'label=""'));
        $this->feld($komponente, $leer);
        $this->assertSame(0, $this->anzahl($leer, '//label'));
    }

    // Randfall 1
    #[DataProvider('komponenten')]
    public function test_leeres_label_erscheint_nicht_als_attribut_am_feld(string $komponente): void
    {
        $leer = $this->rendern(self::aufruf($komponente, 'schlicht', 'label=""'));

        $feld = $this->feld($komponente, $leer);
        $this->assertSame('ort', $feld->getAttribute('name'));
        $this->assertFalse($feld->hasAttribute('label'), "Attribut label am Feld:\n{$leer}");
        $this->assertSame(0, $this->anzahl($leer, '//*[@label]'));
    }

    // Randfall 1
    #[DataProvider('komponentenUndVarianten')]
    public function test_leeres_label_ist_byte_gleich_zum_bisherigen(string $komponente, string $variante): void
    {
        $this->assertArrayHasKey("{$komponente} {$variante}", self::REFERENZ);
        $this->vorbereiten($variante);

        $html = $this->rendern(self::aufruf($komponente, $variante, 'label=""'));

        $this->assertSame(self::REFERENZ["{$komponente} {$variante}"], $html);
    }

    // Randfall 2
    #[DataProvider('komponenten')]
    public function test_array_name_ergibt_for_mit_unterstrich(string $komponente): void
    {
        $html = $this->rendern("<x-kit::{$komponente} name=\"adresse[ort]\" label=\"Ort\"".($komponente === 'combobox' ? ' :options="$optionen"' : '').' />');

        $label = $this->element($html, '//label');
        $feld = $this->feld($komponente, $html);
        $this->assertSame('adresse[ort]', $feld->getAttribute('name'));
        $this->assertSame('adresse_ort', $feld->getAttribute('id'));
        $this->assertSame('adresse_ort', $label->getAttribute('for'));
    }
}
