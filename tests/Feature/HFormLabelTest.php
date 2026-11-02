<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use DOMDocument;
use DOMXPath;
use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Dashboard #388, #476: checkBox() setzt id und for gleich, label() gibt Attribute aus.
 */
final class HFormLabelTest extends TestCase
{
    private const PAYLOAD = '"><script>alert(\'x\')</script>&';

    // ---------------------------------------------------------------- Hilfen

    private function dom(string $html): DOMXPath
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>'.$html.'</body></html>');
        libxml_clear_errors();

        return new DOMXPath($doc);
    }

    /**
     * @return list<string>
     */
    private function struktur(DOMXPath $xpath): array
    {
        $liste = [];
        foreach ($xpath->query('//body//*') as $element) {
            $namen = [];
            foreach ($element->attributes as $attribut) {
                $namen[] = $attribut->name;
            }
            sort($namen);
            $liste[] = $element->nodeName.'['.implode(',', $namen).']';
        }

        return $liste;
    }

    private function inputTag(string $html): string
    {
        $this->assertSame(1, preg_match('/<input\b[^>]*>/', $html, $m), 'Aufbau: genau ein input-Tag in '.$html);

        return $m[0];
    }

    /** Entfernt das id-Attribut und glättet Leerraum; sonst bleibt die Ausgabe, wie sie ist. */
    private function ohneId(string $html): string
    {
        return $this->glatt((string) preg_replace('/ id="[^"]*"/', '', $html));
    }

    private function glatt(string $html): string
    {
        return str_replace(' >', '>', (string) preg_replace('/\s+/', ' ', $html));
    }

    // ------------------------------------------------------------------ AK1

    // AK1
    public function test_checkbox_nimmt_die_id_aus_attributes_und_for_zeigt_darauf(): void
    {
        $html = (string) HForm::checkBox('name1', 'Beschriftung', 'v', false, false, '', ['id' => 'eigene-id']);
        $xpath = $this->dom($html);

        $this->assertSame('eigene-id', $xpath->evaluate('string(//input/@id)'));
        $this->assertSame('eigene-id', $xpath->evaluate('string(//label/@for)'));
        $this->assertSame('name1', $xpath->evaluate('string(//input/@name)'), 'Gegenprobe: id ist nicht der name');
    }

    // AK1
    public function test_checkbox_mit_id_in_attributes_hat_genau_ein_id_attribut(): void
    {
        $html = (string) HForm::checkBox('name1', 'Beschriftung', 'v', false, false, '', ['id' => 'eigene-id', 'data-x' => '1']);

        $this->assertSame(1, preg_match_all('/\sid=/', $this->inputTag($html)), 'id-Attribute im input: '.$html);
        $this->assertSame(1, preg_match_all('/\sid=/', $html), 'id-Attribute in der Ausgabe: '.$html);
        $this->assertStringContainsString('data-x="1"', $html, 'Gegenprobe: übrige Attribute bleiben');
    }

    // AK1
    public function test_checkbox_mit_id_und_klammername_nimmt_die_id_aus_attributes(): void
    {
        $xpath = $this->dom((string) HForm::checkBox('rechte[]', 'R', '1', false, false, '', ['id' => 'recht-1']));

        $this->assertSame('recht-1', $xpath->evaluate('string(//input/@id)'));
        $this->assertSame('recht-1', $xpath->evaluate('string(//label/@for)'));
        $this->assertSame('rechte[]', $xpath->evaluate('string(//input/@name)'));
    }

    // ------------------------------------------------------------------ AK2

    // AK2
    public function test_checkbox_ohne_id_bekommt_den_namen_als_id_und_for(): void
    {
        $xpath = $this->dom((string) HForm::checkBox('aktiv', 'Aktiv', '1', false, false, '', ['data-x' => '1']));

        $this->assertSame('aktiv', $xpath->evaluate('string(//input/@id)'));
        $this->assertSame('aktiv', $xpath->evaluate('string(//label/@for)'));
    }

    // AK2
    public function test_checkbox_ohne_attributes_bekommt_den_namen_als_id_und_hat_genau_ein_id_attribut(): void
    {
        $html = (string) HForm::checkBox('aktiv', 'Aktiv');
        $xpath = $this->dom($html);

        $this->assertSame('aktiv', $xpath->evaluate('string(//input/@id)'));
        $this->assertSame('aktiv', $xpath->evaluate('string(//label/@for)'));
        $this->assertSame(1, preg_match_all('/\sid=/', $html), 'genau ein id-Attribut: '.$html);
    }

    // AK2
    public function test_checkbox_mit_klammername_ohne_id_nimmt_den_namen_unveraendert_als_id(): void
    {
        $html = (string) HForm::checkBox('rechte[]', 'R', '1');
        $xpath = $this->dom($html);

        $this->assertStringContainsString('id="rechte[]"', $html);
        $this->assertSame('rechte[]', $xpath->evaluate('string(//input/@id)'));
        $this->assertSame('rechte[]', $xpath->evaluate('string(//label/@for)'));
    }

    // AK2
    public function test_mehrere_boxen_einer_gruppe_bekommen_ihre_id_ueber_attributes(): void
    {
        $a = $this->dom((string) HForm::checkBox('rechte[]', 'A', '1', false, false, '', ['id' => 'recht-a']));
        $b = $this->dom((string) HForm::checkBox('rechte[]', 'B', '2', false, false, '', ['id' => 'recht-b']));

        $this->assertSame('recht-a', $a->evaluate('string(//input/@id)'));
        $this->assertSame('recht-a', $a->evaluate('string(//label/@for)'));
        $this->assertSame('recht-b', $b->evaluate('string(//input/@id)'));
        $this->assertSame('recht-b', $b->evaluate('string(//label/@for)'));
    }

    // ------------------------------------------------------------ Randfälle

    // Randfall 1
    public function test_checkbox_mit_leerem_namen_und_ohne_id_hat_kein_id_attribut_und_for_leer(): void
    {
        $html = (string) HForm::checkBox('', 'L', '1');
        $xpath = $this->dom($html);

        $this->assertSame(1, $xpath->query('//label[@for]')->length, 'Aufbau: label mit for vorhanden: '.$html);
        $this->assertSame('', $xpath->evaluate('string(//label/@for)'));
        $this->assertStringContainsString('for=""', $html);
        $this->assertSame(0, $xpath->query('//input[@id]')->length, 'input hat ein id-Attribut: '.$html);
        $this->assertStringNotContainsString(' id=', $html);
    }

    // Randfall 1
    public function test_checkbox_ohne_name_parameter_hat_kein_id_attribut(): void
    {
        $html = (string) HForm::checkBox();

        $this->assertStringContainsString('for=""', $html, 'Aufbau');
        $this->assertStringNotContainsString(' id=', $html);
    }

    // Randfall 2
    public function test_checkbox_mit_leerer_id_in_attributes_nimmt_den_namen_als_id(): void
    {
        $html = (string) HForm::checkBox('aktiv', 'Aktiv', '1', false, false, '', ['id' => '']);
        $xpath = $this->dom($html);

        $this->assertSame('aktiv', $xpath->evaluate('string(//input/@id)'));
        $this->assertSame('aktiv', $xpath->evaluate('string(//label/@for)'));
        $this->assertSame(1, preg_match_all('/\sid=/', $html), 'genau ein id-Attribut: '.$html);
    }

    // Randfall 2
    public function test_checkbox_mit_leerer_id_und_leerem_namen_hat_kein_id_attribut(): void
    {
        $html = (string) HForm::checkBox('', 'L', '1', false, false, '', ['id' => '', 'data-x' => '1']);

        $this->assertStringContainsString('data-x="1"', $html, 'Aufbau: Attribute werden ausgegeben');
        $this->assertStringNotContainsString(' id=', $html);
        $this->assertStringContainsString('for=""', $html);
    }

    // ------------------------------------------------------------------ AK3

    // AK3
    public function test_label_gibt_attribute_im_label_element_aus(): void
    {
        $html = (string) HForm::label('Text', 'fid', 'cls', ['data-x' => '1']);
        $xpath = $this->dom($html);

        $this->assertSame('1', $xpath->evaluate('string(//label/@data-x)'), $html);
        $this->assertStringContainsString('data-x="1"', $html);
        $this->assertSame('fid', $xpath->evaluate('string(//label/@for)'), 'Gegenprobe: for bleibt');
        $this->assertSame('form-check-label cls', $xpath->evaluate('string(//label/@class)'));
        $this->assertSame('Text', $xpath->evaluate('string(//label)'));
    }

    // AK3
    public function test_label_gibt_mehrere_attribute_aus(): void
    {
        $html = (string) HForm::label('T', 'f', '', ['data-a' => '1', 'aria-label' => 'y', '@click' => 'z']);
        $xpath = $this->dom($html);

        $this->assertSame('1', $xpath->evaluate('string(//label/@data-a)'));
        $this->assertSame('y', $xpath->evaluate('string(//label/@aria-label)'));
        $this->assertStringContainsString(' @click="z"', $html);
    }

    // AK3
    public function test_label_verwirft_ungueltige_attributschluessel(): void
    {
        $basis = (string) HForm::label('T', 'f', 'c', ['data-ok' => '1', 'aria-label' => 'y']);
        $this->assertStringContainsString('data-ok="1"', $basis, 'Aufbau: gültige Schlüssel erscheinen');
        $this->assertStringContainsString('aria-label="y"', $basis);

        foreach (['a b', 'a"b', "a'b", 'a>b', 'a/b', 'a=b', ' ', '', "data-a\n", ' data-a', "data-a\t", 'data-a ', "\ndata-a", "data-a\r\n", "\tdata-a"] as $schluessel) {
            $html = (string) HForm::label('T', 'f', 'c', ['data-ok' => '1', $schluessel => 'WERT_ZZ', 'aria-label' => 'y']);

            $this->assertStringNotContainsString('WERT_ZZ', $html, 'Paar mit Schlüssel "'.$schluessel.'" wurde ausgegeben: '.$html);
            $this->assertSame($this->struktur($this->dom($basis)), $this->struktur($this->dom($html)), 'Schlüssel "'.$schluessel.'" erzeugt zusätzliche Attribute: '.$html);
            $this->assertStringContainsString('data-ok="1"', $html);
            $this->assertStringContainsString('aria-label="y"', $html);
        }
    }

    // ------------------------------------------------------------------ AK4

    // AK4
    public function test_checkbox_id_aus_attributes_ist_wie_e_escaped(): void
    {
        $basis = (string) HForm::checkBox('n', 'L', '1', false, false, '', ['id' => 'harmlos']);
        $html = (string) HForm::checkBox('n', 'L', '1', false, false, '', ['id' => self::PAYLOAD]);
        $xpath = $this->dom($html);

        $this->assertSame('harmlos', $this->dom($basis)->evaluate('string(//input/@id)'), 'Gegenprobe: Aufbau greift');
        $this->assertSame(self::PAYLOAD, $xpath->evaluate('string(//input/@id)'));
        $this->assertSame(self::PAYLOAD, $xpath->evaluate('string(//label/@for)'));
        $this->assertStringContainsString('id="'.e(self::PAYLOAD).'"', $html);
        $this->assertStringContainsString('for="'.e(self::PAYLOAD).'"', $html);
        $this->assertSame($this->struktur($this->dom($basis)), $this->struktur($xpath), 'Zusätzliche Attribute oder Elemente: '.$html);
        $this->assertSame(0, $xpath->query('//script')->length);
    }

    // AK4
    public function test_checkbox_id_aus_name_ist_wie_e_escaped(): void
    {
        $basis = (string) HForm::checkBox('harmlos');
        $html = (string) HForm::checkBox(self::PAYLOAD);
        $xpath = $this->dom($html);

        $this->assertSame('harmlos', $this->dom($basis)->evaluate('string(//input/@id)'), 'Gegenprobe: Aufbau greift');
        $this->assertSame(self::PAYLOAD, $xpath->evaluate('string(//input/@id)'));
        $this->assertSame(self::PAYLOAD, $xpath->evaluate('string(//label/@for)'));
        $this->assertStringContainsString('id="'.e(self::PAYLOAD).'"', $html);
        $this->assertSame($this->struktur($this->dom($basis)), $this->struktur($xpath), 'Zusätzliche Attribute oder Elemente: '.$html);
        $this->assertSame(0, $xpath->query('//script')->length);
    }

    // AK4
    public function test_label_attributwert_ist_wie_e_escaped(): void
    {
        $basis = (string) HForm::label('T', 'f', 'c', ['data-x' => 'harmlos']);
        $html = (string) HForm::label('T', 'f', 'c', ['data-x' => self::PAYLOAD]);
        $xpath = $this->dom($html);

        $this->assertSame('harmlos', $this->dom($basis)->evaluate('string(//label/@data-x)'), 'Gegenprobe: Aufbau greift');
        $this->assertSame(self::PAYLOAD, $xpath->evaluate('string(//label/@data-x)'));
        $this->assertStringContainsString('data-x="'.e(self::PAYLOAD).'"', $html);
        $this->assertSame($this->struktur($this->dom($basis)), $this->struktur($xpath), 'Zusätzliche Attribute oder Elemente: '.$html);
        $this->assertSame(0, $xpath->query('//script')->length);
    }

    // AK4
    public function test_checkbox_attributwert_neben_der_id_ist_wie_e_escaped(): void
    {
        $html = (string) HForm::checkBox('n', 'L', '1', false, false, '', ['id' => 'i', 'data-x' => self::PAYLOAD]);
        $xpath = $this->dom($html);

        $this->assertSame('i', $xpath->evaluate('string(//input/@id)'), 'Aufbau');
        $this->assertSame(self::PAYLOAD, $xpath->evaluate('string(//input/@data-x)'));
        $this->assertStringContainsString('data-x="'.e(self::PAYLOAD).'"', $html);
        $this->assertSame(0, $xpath->query('//script')->length);
    }

    // ------------------------------------------------------------------ AK5

    /**
     * Erwartet ist die Ausgabe des Stands vor der Änderung (ohne id-Attribut am input).
     *
     * @return array<string, array{0: callable, 1: string}>
     */
    public static function checkboxReferenzen(): array
    {
        $attr = ['data-x' => '1'];

        return [
            'angehakt' => [fn () => HForm::checkBox('n', 'Label', 'v', true, false, 'cls', $attr), '<div class="form-check"><input type="checkbox" name="n" class="form-check-input cls" value="v" checked data-x="1" ><label class="form-check-label" for="n">Label</label></div>'],
            'inline' => [fn () => HForm::checkBox('n', 'Label', 'v', false, true, 'cls', $attr), '<div class="form-check form-check-inline"><input type="checkbox" name="n" class="form-check-input cls" value="v"  data-x="1" ><label class="form-check-label" for="n">Label</label></div>'],
            'ohne Attribute' => [fn () => HForm::checkBox('n', 'Label', 'v'), '<div class="form-check"><input type="checkbox" name="n" class="form-check-input " value="v"  ><label class="form-check-label" for="n">Label</label></div>'],
            'id in Attributes' => [fn () => HForm::checkBox('n', 'Label', 'v', false, false, 'cls', ['id' => 'x', 'data-x' => '1']), '<div class="form-check"><input type="checkbox" name="n" class="form-check-input cls" value="v"  data-x="1" ><label class="form-check-label" for="x">Label</label></div>'],
            'Text escaped' => [fn () => HForm::checkBox('n', '<b>&', 'v&'), '<div class="form-check"><input type="checkbox" name="n" class="form-check-input " value="v&amp;"  ><label class="form-check-label" for="n">&lt;b&gt;&amp;</label></div>'],
        ];
    }

    // AK5
    #[DataProvider('checkboxReferenzen')]
    public function test_checkbox_ausgabe_ist_bis_auf_die_id_unveraendert(callable $render, string $erwartet): void
    {
        $html = (string) $render();

        $this->assertStringContainsString(' id="', $html, 'Aufbau: das id-Attribut ist die einzige Abweichung');
        $this->assertSame($this->glatt($erwartet), $this->ohneId($html));
    }

    /**
     * @return array<string, array{0: callable, 1: string}>
     */
    public static function labelReferenzen(): array
    {
        return [
            'mit class' => [fn () => HForm::label('Beschriftung', 'fid', 'cls'), '<label class="form-check-label cls" for="fid">Beschriftung</label>'],
            'ohne class' => [fn () => HForm::label('Beschriftung', 'fid'), '<label class="form-check-label " for="fid">Beschriftung</label>'],
            'ohne for' => [fn () => HForm::label('Beschriftung'), '<label class="form-check-label " for="">Beschriftung</label>'],
            'leeres Attribut-Array' => [fn () => HForm::label('B', 'f', 'c', []), '<label class="form-check-label c" for="f">B</label>'],
            'nur ungültige Schlüssel' => [fn () => HForm::label('B', 'f', 'c', ['a b' => '1', '' => '2']), '<label class="form-check-label c" for="f">B</label>'],
            'Text und for escaped' => [fn () => HForm::label('<b>&', 'a"b', 'c"d'), '<label class="form-check-label c&quot;d" for="a&quot;b">&lt;b&gt;&amp;</label>'],
        ];
    }

    // AK5
    #[DataProvider('labelReferenzen')]
    public function test_label_ausgabe_ohne_attribute_ist_zeichen_fuer_zeichen_gleich(callable $render, string $erwartet): void
    {
        $this->assertSame($erwartet, (string) $render());
    }

    // AK5
    public function test_label_mit_attributen_aendert_nur_die_attribute(): void
    {
        $mit = (string) HForm::label('Beschriftung', 'fid', 'cls', ['data-x' => '1']);
        $ohne = '<label class="form-check-label cls" for="fid">Beschriftung</label>';

        $this->assertStringContainsString('data-x="1"', $mit, 'Aufbau: Attribut erscheint');
        $this->assertSame($this->glatt($ohne), $this->glatt(str_replace('data-x="1"', '', $mit)));
    }

    // AK5
    public function test_radiobox_ausgabe_bleibt_gleich(): void
    {
        $this->assertSame(
            '<div class="form-check form-check-inline"><input type="radio" id="i" name="g" class="form-check-input cls" value="v" checked data-x="1" ><label class="form-check-label" for="i">Label</label></div>',
            (string) HForm::radioBox('i', 'g', 'Label', 'v', true, true, 'cls', ['data-x' => '1'])
        );
    }

    // AK5
    public function test_hgroupstart_ausgabe_bleibt_gleich(): void
    {
        $this->assertSame('<div class="input-group mb-3 x" data-x="1" >', (string) HForm::hGroupStart('mb-3 x', ['data-x' => '1']));
    }
}
