<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Dashboard #360: HForm escapt Werte, Attribute und Text.
 */
final class HFormEscapeTest extends TestCase
{
    private const ATTRIBUT_PAYLOAD = '"><script>alert(\'x\')</script>&';

    private const TEXT_PAYLOAD = '<b>x</b> & <i>y</i>';

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
     * Elemente samt Attributnamen im Body.
     *
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

    private function vorlage(string $vorlage, string $wert): string
    {
        return str_replace(['{v}', '{asset}'], [$wert, URL::asset('/')], $vorlage);
    }

    /**
     * @param  list<array{0: string, 1: string}>  $pruefungen
     */
    private function pruefeEscaping(callable $render, array $pruefungen, string $payload): void
    {
        $basisHtml = (string) $render('harmlos');
        $html = (string) $render($payload);
        $basis = $this->dom($basisHtml);
        $xpath = $this->dom($html);

        // Gegenprobe: Der Aufbau greift, die Prüfungen finden im harmlosen Fall ihren Wert.
        foreach ($pruefungen as [$ausdruck, $vorlage]) {
            $this->assertSame($this->vorlage($vorlage, 'harmlos'), $basis->evaluate($ausdruck), "Aufbau: $ausdruck bei harmlosem Wert in $basisHtml");
        }

        $this->assertSame($this->struktur($basis), $this->struktur($xpath), "Zusätzliche Attribute oder Elemente durch ungeescapten Wert: $html");
        foreach ($pruefungen as [$ausdruck, $vorlage]) {
            $this->assertSame($this->vorlage($vorlage, $payload), $xpath->evaluate($ausdruck), "Wert nicht unverändert im Dokument: $html");
        }
        $this->assertStringContainsString(e($payload), $html, 'Wert ist nicht wie e() escaped');
    }

    /**
     * @return list<object>
     */
    private static function objekte(mixed $id, mixed $bez): array
    {
        return [new class($id, $bez)
        {
            public function __construct(private mixed $id, private mixed $bez) {}

            public function getFieldID(): mixed
            {
                return $this->id;
            }

            public function getFieldBez(): mixed
            {
                return $this->bez;
            }
        }];
    }

    // ------------------------------------------------------------ Provider

    /**
     * Fälle: [Aufruf(Wert), Prüfungen, Wert ist Text ohne Zahlenformat].
     *
     * @return array<string, array{0: callable, 1: list<array{0: string, 1: string}>, 2: bool}>
     */
    public static function attributFaelle(): array
    {
        $a = ['string(//*[@data-x][1]/@data-x)', '{v}'];
        $f = [];

        $f['open action'] = [fn ($v) => HForm::open($v), [['string(//form/@action)', '{v}']], true];
        $f['open attributes'] = [fn ($v) => HForm::open('/x', 'post', false, ['data-x' => $v]), [$a], true];
        $f['script pfad'] = [fn ($v) => HForm::script($v), [['string(//script/@src)', '{asset}{v}']], true];
        $f['style pfad'] = [fn ($v) => HForm::style($v), [['string(//link/@href)', '{asset}{v}']], true];

        $f['text name'] = [fn ($v) => HForm::text($v), [['string(//input/@name)', '{v}']], true];
        $f['text value'] = [fn ($v) => HForm::text('n', $v), [['string(//input/@value)', '{v}']], true];
        $f['text class'] = [fn ($v) => HForm::text('n', '', $v), [['string(//input/@class)', 'form-control input-text {v}']], true];
        $f['text attributes'] = [fn ($v) => HForm::text('n', '', '', ['data-x' => $v]), [$a], true];

        $f['hidden name'] = [fn ($v) => HForm::hidden($v), [['string(//input/@name)', '{v}']], true];
        $f['hidden value'] = [fn ($v) => HForm::hidden('n', $v), [['string(//input/@value)', '{v}']], true];
        $f['hidden attributes'] = [fn ($v) => HForm::hidden('n', '', ['data-x' => $v]), [$a], true];

        $f['euro name'] = [fn ($v) => HForm::euro($v), [['string(//input/@name)', '{v}'], ['string(//input/@id)', '{v}']], true];
        $f['euro value'] = [fn ($v) => HForm::euro('n', $v), [['string(//input/@value)', '{v}']], false];
        $f['euro class'] = [fn ($v) => HForm::euro('n', '', $v), [['string(//input/@class)', 'form-control input-euro {v}']], true];
        $f['euro attributes'] = [fn ($v) => HForm::euro('n', '', '', ['data-x' => $v]), [$a], true];

        foreach (['prozent', 'prozent1', 'prozent2'] as $m) {
            $f["$m name"] = [fn ($v) => HForm::$m($v), [['string(//input/@name)', '{v}']], true];
            $f["$m value"] = [fn ($v) => HForm::$m('n', $v), [['string(//input/@value)', '{v}']], false];
            $f["$m class"] = [fn ($v) => HForm::$m('n', '', $v), [['string(//input/@class)', 'form-control input-prozent {v}']], true];
            $f["$m attributes"] = [fn ($v) => HForm::$m('n', '', '', ['data-x' => $v]), [$a], true];
        }

        $f['int name'] = [fn ($v) => HForm::int($v), [['string(//input/@name)', '{v}']], true];
        $f['int value'] = [fn ($v) => HForm::int('n', $v), [['string(//input/@value)', '{v}']], false];
        $f['int class'] = [fn ($v) => HForm::int('n', '', $v), [['string(//input/@class)', 'form-control input-int {v}']], true];
        $f['int attributes'] = [fn ($v) => HForm::int('n', '', '', ['data-x' => $v]), [$a], true];

        $f['number name'] = [fn ($v) => HForm::number($v), [['string(//input/@name)', '{v}']], true];
        $f['number value'] = [fn ($v) => HForm::number('n', $v), [['string(//input/@value)', '{v}']], false];
        $f['number class'] = [fn ($v) => HForm::number('n', '', 0, $v), [['string(//input/@class)', 'form-control input-zahl  {v}']], true];
        $f['number attributes'] = [fn ($v) => HForm::number('n', '', 0, '', ['data-x' => $v]), [$a], true];

        $f['date name'] = [fn ($v) => HForm::date($v), [['string(//input/@name)', '{v}']], true];
        $f['date class'] = [fn ($v) => HForm::date('n', '', $v), [['string(//input/@class)', 'form-control input-date {v}']], true];
        $f['date attributes'] = [fn ($v) => HForm::date('n', '', '', ['data-x' => $v]), [$a], true];

        $f['password name'] = [fn ($v) => HForm::password($v), [['string(//input/@name)', '{v}']], true];
        $f['password class'] = [fn ($v) => HForm::password('n', $v), [['string(//input/@class)', 'form-control input-passw {v}']], true];
        $f['password attributes'] = [fn ($v) => HForm::password('n', '', ['data-x' => $v]), [$a], true];

        $f['textArea name'] = [fn ($v) => HForm::textArea($v), [['string(//textarea/@name)', '{v}'], ['string(//textarea/@id)', '{v}']], true];
        $f['textArea class'] = [fn ($v) => HForm::textArea('n', '', $v), [['string(//textarea/@class)', 'form-control {v}']], true];
        $f['textArea rows'] = [fn ($v) => HForm::textArea('n', '', '', $v), [['string(//textarea/@rows)', '{v}']], true];
        $f['textArea attributes'] = [fn ($v) => HForm::textArea('n', '', '', 3, ['data-x' => $v]), [$a], true];

        $f['label forID'] = [fn ($v) => HForm::label('L', $v), [['string(//label/@for)', '{v}']], true];
        $f['label class'] = [fn ($v) => HForm::label('L', 'f', $v), [['string(//label/@class)', 'form-check-label {v}']], true];

        $f['checkBox name'] = [fn ($v) => HForm::checkBox($v), [['string(//input/@name)', '{v}'], ['string(//label/@for)', '{v}']], true];
        $f['checkBox value'] = [fn ($v) => HForm::checkBox('n', 'L', $v), [['string(//input/@value)', '{v}']], true];
        $f['checkBox class'] = [fn ($v) => HForm::checkBox('n', 'L', 'x', false, false, $v), [['string(//input/@class)', 'form-check-input {v}']], true];
        $f['checkBox attributes'] = [fn ($v) => HForm::checkBox('n', 'L', 'x', false, false, '', ['data-x' => $v]), [$a], true];

        $f['radioBox id'] = [fn ($v) => HForm::radioBox($v), [['string(//input/@id)', '{v}'], ['string(//label/@for)', '{v}']], true];
        $f['radioBox group'] = [fn ($v) => HForm::radioBox('i', $v), [['string(//input/@name)', '{v}']], true];
        $f['radioBox value'] = [fn ($v) => HForm::radioBox('i', 'g', 'L', $v), [['string(//input/@value)', '{v}']], true];
        $f['radioBox class'] = [fn ($v) => HForm::radioBox('i', 'g', 'L', 'x', false, false, $v), [['string(//input/@class)', 'form-check-input {v}']], true];
        $f['radioBox attributes'] = [fn ($v) => HForm::radioBox('i', 'g', 'L', 'x', false, false, '', ['data-x' => $v]), [$a], true];

        $f['comboBox name'] = [fn ($v) => HForm::comboBox($v, []), [['string(//select/@name)', '{v}']], true];
        $f['comboBox class'] = [fn ($v) => HForm::comboBox('n', [], 0, '', $v), [['string(//select/@class)', 'form-control {v}']], true];
        $f['comboBox attributes'] = [fn ($v) => HForm::comboBox('n', [], 0, '', '', ['data-x' => $v]), [$a], true];
        $f['comboBox option value'] = [fn ($v) => HForm::comboBox('n', self::objekte($v, 'B'), 'keine', 'Erst'), [['string(//option[last()]/@value)', '{v}']], true];

        $f['listBox name'] = [fn ($v) => HForm::listBox($v, []), [['string(//select/@name)', '{v}']], true];
        $f['listBox size'] = [fn ($v) => HForm::listBox('n', [], 1, $v), [['string(//select/@size)', '{v}']], true];
        $f['listBox class'] = [fn ($v) => HForm::listBox('n', [], 1, 5, $v), [['string(//select/@class)', 'form-control {v}']], true];
        $f['listBox attributes'] = [fn ($v) => HForm::listBox('n', [], 1, 5, '', ['data-x' => $v]), [$a], true];
        $f['listBox option value'] = [fn ($v) => HForm::listBox('n', self::objekte($v, 'B'), 'keine'), [['string(//option/@value)', '{v}']], true];

        $f['comboBoxArray name'] = [fn ($v) => HForm::comboBoxArray($v, []), [['string(//select/@name)', '{v}'], ['string(//select/@id)', '{v}']], true];
        $f['comboBoxArray class'] = [fn ($v) => HForm::comboBoxArray('n', [], 0, '', $v), [['string(//select/@class)', 'form-control {v}']], true];
        $f['comboBoxArray attributes'] = [fn ($v) => HForm::comboBoxArray('n', [], 0, '', '', ['data-x' => $v]), [$a], true];
        $f['comboBoxArray option value'] = [fn ($v) => HForm::comboBoxArray('n', [[$v, 'B']], 'keine'), [['string(//option/@value)', '{v}']], true];

        $f['listBoxArray name'] = [fn ($v) => HForm::listBoxArray($v, []), [['string(//select/@name)', '{v}']], true];
        $f['listBoxArray size'] = [fn ($v) => HForm::listBoxArray('n', [], 0, $v), [['string(//select/@size)', '{v}']], true];
        $f['listBoxArray class'] = [fn ($v) => HForm::listBoxArray('n', [], 0, 5, $v), [['string(//select/@class)', 'form-control {v}']], true];
        $f['listBoxArray attributes'] = [fn ($v) => HForm::listBoxArray('n', [], 0, 5, '', ['data-x' => $v]), [$a], true];
        $f['listBoxArray option value'] = [fn ($v) => HForm::listBoxArray('n', [[$v, 'B']], 'keine'), [['string(//option/@value)', '{v}']], true];

        $f['hGroupStart class'] = [fn ($v) => HForm::hGroupStart($v), [['string(//div/@class)', 'input-group {v}']], true];
        $f['hGroupStart attributes'] = [fn ($v) => HForm::hGroupStart('c', ['data-x' => $v]), [$a], true];

        return $f;
    }

    /**
     * @return array<string, array{0: callable, 1: list<array{0: string, 1: string}>}>
     */
    public static function textFaelle(): array
    {
        $f = [];

        $f['label text'] = [fn ($v) => HForm::label($v), [['string(//label)', '{v}']]];
        $f['checkBox label'] = [fn ($v) => HForm::checkBox('n', $v), [['string(//label)', '{v}']]];
        $f['radioBox label'] = [fn ($v) => HForm::radioBox('i', 'g', $v), [['string(//label)', '{v}']]];
        $f['comboBox optionstext'] = [fn ($v) => HForm::comboBox('n', self::objekte('1', $v)), [['string(//option)', '{v}']]];
        $f['comboBox firstEntry'] = [fn ($v) => HForm::comboBox('n', [], 0, $v), [['string(//option[1])', '{v}']]];
        $f['listBox optionstext'] = [fn ($v) => HForm::listBox('n', self::objekte('1', $v)), [['string(//option)', '{v}']]];
        $f['comboBoxArray optionstext'] = [fn ($v) => HForm::comboBoxArray('n', [[1, $v]]), [['string(//option)', '{v}']]];
        $f['comboBoxArray firstEntry'] = [fn ($v) => HForm::comboBoxArray('n', [], 0, $v), [['string(//option[1])', '{v}']]];
        $f['listBoxArray optionstext'] = [fn ($v) => HForm::listBoxArray('n', [[1, $v]]), [['string(//option)', '{v}']]];
        $f['textArea inhalt'] = [fn ($v) => HForm::textArea('n', $v), [['string(//textarea)', '{v}']]];

        return $f;
    }

    /**
     * @return array<string, array{0: callable}>
     */
    public static function attributMethoden(): array
    {
        return [
            'open' => [fn (array $a) => HForm::open('/x', 'post', false, $a)],
            'text' => [fn (array $a) => HForm::text('n', '', '', $a)],
            'hidden' => [fn (array $a) => HForm::hidden('n', '', $a)],
            'euro' => [fn (array $a) => HForm::euro('n', '', '', $a)],
            'prozent' => [fn (array $a) => HForm::prozent('n', '', '', $a)],
            'prozent1' => [fn (array $a) => HForm::prozent1('n', '', '', $a)],
            'prozent2' => [fn (array $a) => HForm::prozent2('n', '', '', $a)],
            'int' => [fn (array $a) => HForm::int('n', '', '', $a)],
            'number' => [fn (array $a) => HForm::number('n', '', 0, '', $a)],
            'date' => [fn (array $a) => HForm::date('n', '', '', $a)],
            'password' => [fn (array $a) => HForm::password('n', '', $a)],
            'textArea' => [fn (array $a) => HForm::textArea('n', '', '', 3, $a)],
            'checkBox' => [fn (array $a) => HForm::checkBox('n', 'L', 'v', false, false, '', $a)],
            'radioBox' => [fn (array $a) => HForm::radioBox('i', 'g', 'L', 'v', false, false, '', $a)],
            'comboBox' => [fn (array $a) => HForm::comboBox('n', [], 0, '', '', $a)],
            'listBox' => [fn (array $a) => HForm::listBox('n', [], 1, 5, '', $a)],
            'comboBoxArray' => [fn (array $a) => HForm::comboBoxArray('n', [], 0, '', '', $a)],
            'listBoxArray' => [fn (array $a) => HForm::listBoxArray('n', [], 0, 5, '', $a)],
            'hGroupStart' => [fn (array $a) => HForm::hGroupStart('c', $a)],
        ];
    }

    /**
     * Harmlose Referenzausgaben des Stands vor der Änderung. {A} = URL::asset('/').
     *
     * @return array<string, array{0: callable, 1: string}>
     */
    public static function referenzen(): array
    {
        $daten = fn () => self::objekte('2', 'Zwei');
        $opt = "<option value=\"1\">Eins</option>\n<option value=\"2\" selected>Zwei</option>\n";
        $attr = ['data-x' => '1'];
        $feld = fn (string $typ, string $klasse, string $wert, string $ende) => '<input type="text" name="n" class="form-control '.$klasse.'" '.$wert.' data-x="1"'.$ende;
        $gruppe = fn (string $input) => '<div class="input-group">'.$input.'<div class="input-group-append"><span class="input-group-text">%</span></div></div>';
        $zeilen = fn () => [self::objekte('1', 'Eins')[0], self::objekte('2', 'Zwei')[0]];

        return [
            'open' => [fn () => HForm::open('/save', 'post', false, ['data-x' => '1']), '<form id="myform" method="post" action="/save"  data-x="1" >'."\n".'<input type="hidden" name="_token" value="TOKEN" autocomplete="off">'],
            'open mit Datei' => [fn () => HForm::open('/save', 'post', true, ['data-x' => '1']), '<form id="myform" method="post" action="/save" enctype="multipart/form-data" data-x="1" >'."\n".'<input type="hidden" name="_token" value="TOKEN" autocomplete="off">'],
            'close' => [fn () => HForm::close(), '</form>'],
            'script' => [fn () => HForm::script('js/app.js'), '<script src="{A}js/app.js" ></script>'],
            'style' => [fn () => HForm::style('css/app.css'), '<link href="{A}css/app.css" rel="stylesheet">'],
            'public' => [fn () => HForm::public('img/logo.png'), '{A}img/logo.png'],
            'text' => [fn () => HForm::text('n', 'Müller 5%', 'cls', $attr), '<input type="text" name="n" class="form-control input-text cls" value="Müller 5%" data-x="1"  >'],
            'hidden' => [fn () => HForm::hidden('n', 'v', $attr), '<input type="hidden" name="n" value="v" data-x="1"  />'],
            'euro text' => [fn () => HForm::euro('n', 'abc', 'cls', $attr), '<div class="input-group"><input type="text" id="n" name="n" class="form-control input-euro cls" aria-label="Euro" value="abc" data-x="1"  ><div class="input-group-append"><span class="input-group-text"> &euro; </span></div></div>'],
            'euro zahl' => [fn () => HForm::euro('n', 1234.5, 'cls', $attr), '<div class="input-group"><input type="text" id="n" name="n" class="form-control input-euro cls" aria-label="Euro" value="1.234,50" data-x="1"  ><div class="input-group-append"><span class="input-group-text"> &euro; </span></div></div>'],
            'prozent' => [fn () => HForm::prozent('n', 1234.5, 'cls', $attr), $gruppe('<input type="text" name="n" class="form-control input-prozent cls" aria-label="Prozent" value="1.235" data-x="1" >')],
            'prozent1' => [fn () => HForm::prozent1('n', 1234.5, 'cls', $attr), $gruppe('<input type="text" name="n" class="form-control input-prozent cls" aria-label="Prozent" value="1.234,5" data-x="1" >')],
            'prozent2' => [fn () => HForm::prozent2('n', 1234.5, 'cls', $attr), $gruppe('<input type="text" name="n" class="form-control input-prozent cls" aria-label="Prozent" value="1.234,50" data-x="1" >')],
            'prozent text' => [fn () => HForm::prozent('n', 'abc', 'cls', $attr), $gruppe('<input type="text" name="n" class="form-control input-prozent cls" aria-label="Prozent" value="abc" data-x="1" >')],
            'int zahl' => [fn () => HForm::int('n', 1234, 'cls', $attr), $feld('', 'input-int cls', 'value="1.234"', ' >')],
            'int text' => [fn () => HForm::int('n', 'abc', 'cls', $attr), $feld('', 'input-int cls', 'value="abc"', ' >')],
            'number zahl' => [fn () => HForm::number('n', 1234.5, 1, 'cls', $attr), $feld('', 'input-zahl  cls', 'value="1.234,5"', '  >')],
            'number text' => [fn () => HForm::number('n', 'abc', 1, 'cls', $attr), $feld('', 'input-zahl  cls', 'value="abc"', '  >')],
            'date' => [fn () => HForm::date('n', '2026-03-15', 'cls', $attr), $feld('', 'input-date cls', 'value="15.03.2026"', '  >')],
            'date leer' => [fn () => HForm::date('n', '', 'cls', $attr), $feld('', 'input-date cls', 'value=""', '  >')],
            'password' => [fn () => HForm::password('n', 'cls', $attr), '<input type="password" name="n" class="form-control input-passw cls" data-x="1"  autocomplete="new-password">'],
            'textArea' => [fn () => HForm::textArea('n', 'Größe äöü', 'cls', 4, $attr), '<textarea id="n" name="n" class="form-control cls" rows="4" data-x="1" >Größe äöü</textarea>'],
            'label' => [fn () => HForm::label('Beschriftung', 'fid', 'cls'), '<label class="form-check-label cls" for="fid">Beschriftung</label>'],
            'checkBox angehakt' => [fn () => HForm::checkBox('n', 'Label', 'v', true, false, 'cls', $attr), '<div class="form-check"><input type="checkbox" name="n" class="form-check-input cls" value="v" checked data-x="1" ><label class="form-check-label" for="n">Label</label></div>'],
            'checkBox inline' => [fn () => HForm::checkBox('n', 'Label', 'v', false, true, 'cls', $attr), '<div class="form-check form-check-inline"><input type="checkbox" name="n" class="form-check-input cls" value="v"  data-x="1" ><label class="form-check-label" for="n">Label</label></div>'],
            'radioBox' => [fn () => HForm::radioBox('i', 'g', 'Label', 'v', true, true, 'cls', $attr), '<div class="form-check form-check-inline"><input type="radio" id="i" name="g" class="form-check-input cls" value="v" checked data-x="1" ><label class="form-check-label" for="i">Label</label></div>'],
            'radioBox block' => [fn () => HForm::radioBox('i', 'g', 'Label', 'v', false, false, 'cls', $attr), '<div class="form-check"><input type="radio" id="i" name="g" class="form-check-input cls" value="v"  data-x="1" ><label class="form-check-label" for="i">Label</label></div>'],
            'comboBox' => [fn () => HForm::comboBox('n', $zeilen(), 2, 'Bitte', 'cls', $attr), '<select name="n" class="form-control cls" data-x="1" >'."\n".'<option value="0">Bitte</option>'."\n".$opt.'</select>'."\n"],
            'comboBox erster Eintrag gewählt' => [fn () => HForm::comboBox('n', $zeilen(), 0, 'Bitte', 'cls', $attr), '<select name="n" class="form-control cls" data-x="1" >'."\n".'<option value="0" selected>Bitte</option>'."\n".'<option value="1">Eins</option>'."\n".'<option value="2">Zwei</option>'."\n".'</select>'."\n"],
            'comboBox ohne erster Eintrag' => [fn () => HForm::comboBox('n', $zeilen()), '<select name="n" class="form-control " >'."\n".'<option value="1" selected>Eins</option>'."\n".'<option value="2">Zwei</option>'."\n".'</select>'."\n"],
            'listBox' => [fn () => HForm::listBox('n', $zeilen(), 2, 6, 'cls', $attr), '<select multiple size="6" name="n" class="form-control cls" data-x="1" >'."\n".$opt.'</select>'."\n"],
            'comboBoxArray' => [fn () => HForm::comboBoxArray('n', [[1, 'Eins'], [2, 'Zwei']], 2, 'Bitte', 'cls', $attr), '<select id="n" name="n" class="form-control cls" data-x="1" >'."\n".'<option value="0">Bitte</option>'."\n".$opt.'</select>'."\n"],
            'comboBoxArray erster Eintrag gewählt' => [fn () => HForm::comboBoxArray('n', [[1, 'Eins']], 0, 'Bitte', 'cls', $attr), '<select id="n" name="n" class="form-control cls" data-x="1" >'."\n".'<option value="0" selected>Bitte</option>'."\n".'<option value="1">Eins</option>'."\n".'</select>'."\n"],
            'listBoxArray' => [fn () => HForm::listBoxArray('n', [[1, 'Eins'], [2, 'Zwei']], 2, 6, 'cls', $attr), '<select multiple size="6" name="n" class="form-control cls" data-x="1" >'."\n".$opt.'</select>'."\n"],
            'hGroupStart' => [fn () => HForm::hGroupStart('mb-3 x', $attr), '<div class="input-group mb-3 x" data-x="1" >'],
            'hGroupEnde' => [fn () => HForm::hGroupEnde(), "</div>\n"],
            'com' => [fn () => HForm::com('Hinweis 1'), '<!-- Hinweis 1 -->'],
            'comStart' => [fn () => HForm::comStart(), '<!-- '],
            'comEnde' => [fn () => HForm::comEnde(), ' -->'],
        ];
    }

    // ---------------------------------------------------------------- AK1

    // AK1
    #[DataProvider('attributFaelle')]
    public function test_attributwert_ist_escaped_und_erzeugt_nichts_zusaetzliches(callable $render, array $pruefungen, bool $text): void
    {
        $this->pruefeEscaping($render, $pruefungen, self::ATTRIBUT_PAYLOAD);
    }

    // AK1
    public function test_public_gibt_den_pfad_escaped_aus(): void
    {
        $this->assertSame(URL::asset('/').'img/logo.png', (string) HForm::public('img/logo.png'), 'Gegenprobe: harmloser Pfad');

        $html = (string) HForm::public(self::ATTRIBUT_PAYLOAD);

        $this->assertSame(e(URL::asset('/').self::ATTRIBUT_PAYLOAD), $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    // ---------------------------------------------------------------- AK2

    // AK2
    #[DataProvider('attributMethoden')]
    public function test_ungueltige_attributschluessel_werden_nicht_ausgegeben(callable $render): void
    {
        $gueltig = ['data-ok' => '1', 'aria-label' => 'y'];
        $basis = (string) $render($gueltig);
        $this->assertStringContainsString('data-ok="1"', $basis, 'Aufbau: gültige Schlüssel erscheinen');
        $this->assertStringContainsString('aria-label="y"', $basis);

        foreach (['a b', 'a"b', "a'b", 'a>b', 'a/b', 'a=b', ' ', ''] as $schluessel) {
            $html = (string) $render(['data-ok' => '1', $schluessel => 'WERT_ZZ', 'aria-label' => 'y']);

            $this->assertStringNotContainsString('WERT_ZZ', $html, 'Paar mit Schlüssel "'.$schluessel.'" wurde ausgegeben: '.$html);
            $this->assertSame($this->struktur($this->dom($basis)), $this->struktur($this->dom($html)), 'Schlüssel "'.$schluessel.'" erzeugt zusätzliche Attribute: '.$html);
            $this->assertStringContainsString('data-ok="1"', $html);
            $this->assertStringContainsString('aria-label="y"', $html);
        }
    }

    // AK2
    #[DataProvider('attributMethoden')]
    public function test_gueltige_attributschluessel_bleiben_unveraendert(callable $render): void
    {
        $schluessel = ['data-x', 'aria-label', 'x_y', '@click', ':class', 'v-on:click'];
        $html = (string) $render(array_fill_keys($schluessel, '1'));

        foreach ($schluessel as $name) {
            $this->assertStringContainsString(' '.$name.'="1"', $html);
        }
    }

    // ---------------------------------------------------------------- AK3

    // AK3
    #[DataProvider('textFaelle')]
    public function test_textinhalt_ist_escaped_und_erzeugt_keine_elemente(callable $render, array $pruefungen): void
    {
        $this->pruefeEscaping($render, $pruefungen, self::TEXT_PAYLOAD);
    }

    // AK3
    public function test_kommentartext_ist_escaped(): void
    {
        $this->assertSame('<!-- Hinweis -->', (string) HForm::com('Hinweis'), 'Gegenprobe: harmloser Text');

        $payload = '--><script>alert(1)</script> & <b>';
        $html = (string) HForm::com($payload);

        $this->assertSame('<!-- '.e($payload).' -->', $html);
        $xpath = $this->dom($html);
        $this->assertCount(0, $xpath->query('//script'));
        $this->assertCount(1, $xpath->query('//body/comment()'));
    }

    // ---------------------------------------------------------------- AK4

    // AK4
    #[DataProvider('attributFaelle')]
    public function test_htmlable_als_attributwert_bleibt_unveraendert(callable $render, array $pruefungen, bool $text): void
    {
        $wert = 'x&amp;y';
        $html = (string) $render(new HtmlString($wert));
        $this->assertStringContainsString($wert, $html);
        $this->assertStringNotContainsString('x&amp;amp;y', $html, 'Htmlable wurde erneut escaped');

        // Gegenprobe: derselbe Text als normaler String wird escaped
        $this->assertStringContainsString('x&amp;amp;y', (string) $render($wert), 'Ein normaler String muss escaped werden');
    }

    // AK4
    #[DataProvider('textFaelle')]
    public function test_htmlable_als_text_bleibt_unveraendert(callable $render, array $pruefungen): void
    {
        $wert = '<b>x</b>';
        $html = (string) $render(new HtmlString($wert));
        $this->assertStringContainsString($wert, $html);

        // Gegenprobe: derselbe Text als normaler String wird escaped
        $plain = (string) $render($wert);
        $this->assertStringContainsString('&lt;b&gt;x&lt;/b&gt;', $plain, 'Ein normaler String muss escaped werden');
        $this->assertStringNotContainsString($wert, $plain);
    }

    // AK4
    public function test_htmlable_als_kommentartext_bleibt_unveraendert(): void
    {
        $this->assertSame('<!-- <b>x</b> -->', (string) HForm::com(new HtmlString('<b>x</b>')));
        $this->assertSame('<!-- &lt;b&gt;x&lt;/b&gt; -->', (string) HForm::com('<b>x</b>'), 'Gegenprobe: String wird escaped');
    }

    // AK4
    public function test_htmlable_als_pfad_bei_public_bleibt_unveraendert(): void
    {
        $this->assertSame(URL::asset('/').'a&amp;b', (string) HForm::public(new HtmlString('a&amp;b')));
        $this->assertSame(URL::asset('/').'a&amp;amp;b', (string) HForm::public('a&amp;b'), 'Gegenprobe: String wird escaped');
    }

    // ---------------------------------------------------------------- AK5

    // AK5
    #[DataProvider('referenzen')]
    public function test_harmlose_werte_ergeben_dieselbe_ausgabe_wie_vorher(callable $render, string $erwartet): void
    {
        $html = (string) preg_replace('/(name="_token" value=")[^"]*"/', '$1TOKEN"', (string) $render());

        $this->assertSame(str_replace('{A}', URL::asset('/'), $erwartet), $html);
    }

    // ------------------------------------------------------------- Randfälle

    // Randfall 1
    #[DataProvider('attributFaelle')]
    public function test_null_ergibt_leeren_string_wie_bisher(callable $render, array $pruefungen, bool $text): void
    {
        $this->assertSame((string) $render(''), (string) $render(null));
    }

    // Randfall 1
    public function test_null_als_wert_und_attribut_ergibt_leeren_wert(): void
    {
        $this->assertStringContainsString('value=""', (string) HForm::text('n', null));
        $this->assertStringContainsString('data-x=""', (string) HForm::text('n', 'v', '', ['data-x' => null]));
        $this->assertStringContainsString('value="v"', (string) HForm::text('n', 'v', '', ['data-x' => null]), 'Gegenprobe: Aufbau greift');
    }

    // Randfall 2
    #[DataProvider('attributFaelle')]
    public function test_zahlen_bleiben_unveraendert_als_text(callable $render, array $pruefungen, bool $text): void
    {
        if (! $text) {
            $this->assertTrue(true, 'Zahlenformat der Methode ist durch HFormDateIntTest und AK5 abgedeckt.');

            return;
        }

        foreach ([5, 1.5] as $zahl) {
            $xpath = $this->dom((string) $render($zahl));
            foreach ($pruefungen as [$ausdruck, $vorlage]) {
                $this->assertSame($this->vorlage($vorlage, (string) $zahl), $xpath->evaluate($ausdruck));
            }
        }
    }

    // Randfall 2
    #[DataProvider('textFaelle')]
    public function test_zahlen_als_text_bleiben_unveraendert(callable $render, array $pruefungen): void
    {
        foreach ([5, 1.5] as $zahl) {
            $xpath = $this->dom((string) $render($zahl));
            foreach ($pruefungen as [$ausdruck, $vorlage]) {
                $this->assertSame($this->vorlage($vorlage, (string) $zahl), $xpath->evaluate($ausdruck));
            }
        }
    }

    // Randfall 3
    #[DataProvider('attributFaelle')]
    public function test_bereits_escapte_attributwerte_werden_erneut_escaped(callable $render, array $pruefungen, bool $text): void
    {
        $html = (string) $render('&amp;');
        $xpath = $this->dom($html);

        $this->assertStringContainsString('&amp;amp;', $html);
        foreach ($pruefungen as [$ausdruck, $vorlage]) {
            $this->assertSame($this->vorlage($vorlage, '&amp;'), $xpath->evaluate($ausdruck), 'Wert muss als Rohtext erhalten bleiben: '.$html);
        }
    }

    // Randfall 3
    #[DataProvider('textFaelle')]
    public function test_bereits_escapter_text_wird_erneut_escaped(callable $render, array $pruefungen): void
    {
        $html = (string) $render('&amp;');
        $xpath = $this->dom($html);

        $this->assertStringContainsString('&amp;amp;', $html);
        foreach ($pruefungen as [$ausdruck, $vorlage]) {
            $this->assertSame($this->vorlage($vorlage, '&amp;'), $xpath->evaluate($ausdruck), 'Text muss als Rohtext erhalten bleiben: '.$html);
        }
    }

    // Randfall 3
    public function test_bereits_escapter_kommentar_und_pfad_werden_erneut_escaped(): void
    {
        $this->assertSame('<!-- &amp;amp; -->', (string) HForm::com('&amp;'));
        $this->assertSame(URL::asset('/').'&amp;amp;', (string) HForm::public('&amp;'));
    }
}
