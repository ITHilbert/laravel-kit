<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use ITHilbert\LaravelKit\Helpers\HButton;
use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Dashboard #489, #370, #486: HButton escapt wie HForm, delete ohne globale Funktion,
 * kein Attribut doppelt (HForm und HButton).
 */
final class HButtonEscapeTest extends TestCase
{
    private const PAYLOAD = '"><script>alert(\'x\')</script>&';

    private const TEXT_PAYLOAD = '<b>x</b> & <i>y</i>';

    /** Einzelzeichen aus AK1 plus der kombinierte Angriff. */
    private const ZEICHEN = ['"', '<', '>', '&', "'", self::PAYLOAD];

    // ---------------------------------------------------------------- Hilfen

    private function dom(string $html): DOMXPath
    {
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>');
        libxml_clear_errors();

        return new DOMXPath($doc);
    }

    private function wurzel(string $html): DOMElement
    {
        $knoten = $this->dom($html)->query('//body/*');
        $this->assertGreaterThanOrEqual(1, $knoten->length, 'Aufbau: ein Element in '.$html);

        return $knoten->item(0);
    }

    /**
     * Start-Tags der Ausgabe mit ihren Attributnamen (Reihenfolge wie im Quelltext, nicht dedupliziert).
     * Schlägt fehl, wenn das Markup kaputt ist (ein "<tag" ohne passendes Start-Tag).
     *
     * @return list<array{0: string, 1: list<string>}>
     */
    private function tags(string $html): array
    {
        $muster = '/<([a-zA-Z][a-zA-Z0-9]*)((?:\s+[^\s"\'>\/=]+(?:=(?:"[^"]*"))?)*)\s*\/?>/';
        preg_match_all($muster, $html, $treffer, PREG_SET_ORDER);
        $this->assertSame(preg_match_all('/<[a-zA-Z]/', $html), count($treffer), 'Markup kaputt: '.$html);

        $liste = [];
        foreach ($treffer as $t) {
            preg_match_all('/\s([^\s"\'>\/=]+)(?:=(?:"[^"]*"))?/', $t[2], $namen);
            $liste[] = [strtolower($t[1]), array_map('strtolower', $namen[1])];
        }

        return $liste;
    }

    /** Elemente samt sortierten Attributnamen. */
    private function struktur(string $html): array
    {
        return array_map(function (array $tag): string {
            $namen = $tag[1];
            sort($namen);

            return $tag[0].'['.implode(',', $namen).']';
        }, $this->tags($html));
    }

    private function keinAttributDoppelt(string $html): void
    {
        foreach ($this->tags($html) as [$tag, $namen]) {
            $this->assertSame(
                array_values(array_unique($namen)),
                $namen,
                'Attribut doppelt in <'.$tag.'>: '.$html
            );
        }
    }

    private function glatt(string $html): string
    {
        return trim((string) preg_replace('/\s+(\/?>)/', '$1', (string) preg_replace('/\s+/', ' ', $html)));
    }

    private function ohneOnclick(string $html): string
    {
        return (string) preg_replace('/ onclick="[^"]*"/', '', $html);
    }

    /**
     * Führt den onclick-Code wie ein Browser aus (globaler Gültigkeitsbereich, window = global).
     *
     * @return array{error: ?string, calls: list<list<mixed>>, alerts: list<mixed>}
     */
    private function klick(string $onclick, bool $mitFunktion): array
    {
        $skript = <<<'JS'
const vm = require('node:vm');
let d = '';
process.stdin.on('data', (c) => (d += c)).on('end', () => {
    const { code, mitFunktion } = JSON.parse(d);
    const calls = [];
    const alerts = [];
    const sandbox = { alert: (...a) => alerts.push(a) };
    sandbox.window = sandbox;
    sandbox.self = sandbox;
    sandbox.document = {};
    if (mitFunktion) {
        sandbox.OpenDeleteForm = (...a) => calls.push(a);
    }
    vm.createContext(sandbox);
    let error = null;
    try {
        vm.runInContext('(function (event) {' + code + '\n})', sandbox).call({}, { type: 'click' });
    } catch (e) {
        error = e.name + ': ' + e.message;
    }
    process.stdout.write(JSON.stringify({ error, calls, alerts }));
});
JS;
        $prozess = proc_open(['node', '-e', $skript], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $this->assertIsResource($prozess, 'node muss für den JavaScript-Nachweis verfügbar sein');
        fwrite($pipes[0], json_encode(['code' => $onclick, 'mitFunktion' => $mitFunktion], JSON_THROW_ON_ERROR));
        fclose($pipes[0]);
        $aus = stream_get_contents($pipes[1]);
        $fehler = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($prozess);

        $ergebnis = json_decode((string) $aus, true);
        $this->assertIsArray($ergebnis, 'node-Ausgabe: '.$aus.' '.$fehler);

        return $ergebnis;
    }

    // ---------------------------------------------------------------- Provider

    /**
     * Alle HButton-Methoden: [Name, Aufruf(text, tooltip, class, route, attrs, id)].
     *
     * @return array<string, array{0: callable}>
     */
    public static function methoden(): array
    {
        return [
            'project' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::project($r, $t, $tip, $c, $a)],
            'create' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::create($r, $t, $tip, $c, $a)],
            'show' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::show($r, $t, $tip, $c, $a)],
            'edit' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::edit($r, $t, $tip, $c, $a)],
            'delete' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::delete($i, $t, $tip, $c, $a)],
            'submit' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::submit($t, $tip, $c, $a)],
            'save' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::save($t, $tip, $c, $a)],
            'back' => [fn ($t = 'Text', $tip = 'Tipp', $c = 'cls', $r = '/p', $a = [], $i = 1) => HButton::back($r, $t, $tip, $c, $a)],
        ];
    }

    /**
     * Methode × Parameter für AK1: [Aufruf, Parameter, Attribut mit dem Wert, hat Wirkung].
     *
     * @return array<string, array{0: callable, 1: string, 2: string}>
     */
    public static function attributParameter(): array
    {
        $f = [];
        foreach (self::methoden() as $name => [$aufruf]) {
            $f[$name.' tooltip'] = [$aufruf, 'tooltip', 'title'];
            $f[$name.' attribute'] = [$aufruf, 'attrs', 'data-x'];
            if ($name !== 'edit') {
                $f[$name.' class'] = [$aufruf, 'class', 'class'];
            }
            if (! in_array($name, ['delete', 'submit', 'save'], true)) {
                $f[$name.' route'] = [$aufruf, 'route', 'href'];
            }
        }

        return $f;
    }

    /**
     * Alle HForm- und HButton-Aufrufe für AK6: [Aufruf(attrs), Ausnahme-Namen].
     *
     * @return array<string, array{0: callable, 1: list<string>}>
     */
    public static function attributAufrufe(): array
    {
        $f = [];
        foreach (self::methoden() as $name => [$aufruf]) {
            $f['HButton::'.$name] = [fn (array $a) => $aufruf('Text', 'Tipp', 'cls', '/p', $a, 5), []];
        }
        $f['HButton::create ohne Vorgaben'] = [fn (array $a) => HButton::create('#', 'Erstellen', 'Erstellen', '', $a), []];
        $f['HForm::open'] = [fn (array $a) => HForm::open('/x', 'post', false, $a), []];
        $f['HForm::open Datei'] = [fn (array $a) => HForm::open('/x', 'post', true, $a), []];
        $f['HForm::text'] = [fn (array $a) => HForm::text('n', 'w', 'c', $a), []];
        $f['HForm::hidden'] = [fn (array $a) => HForm::hidden('n', 'w', $a), []];
        $f['HForm::euro'] = [fn (array $a) => HForm::euro('n', 'w', 'c', $a), []];
        $f['HForm::prozent'] = [fn (array $a) => HForm::prozent('n', 'w', 'c', $a), []];
        $f['HForm::prozent1'] = [fn (array $a) => HForm::prozent1('n', 'w', 'c', $a), []];
        $f['HForm::prozent2'] = [fn (array $a) => HForm::prozent2('n', 'w', 'c', $a), []];
        $f['HForm::int'] = [fn (array $a) => HForm::int('n', 'w', 'c', $a), []];
        $f['HForm::number'] = [fn (array $a) => HForm::number('n', 'w', 0, 'c', $a), []];
        $f['HForm::date'] = [fn (array $a) => HForm::date('n', '', 'c', $a), []];
        $f['HForm::password'] = [fn (array $a) => HForm::password('n', 'c', $a), []];
        $f['HForm::textArea'] = [fn (array $a) => HForm::textArea('n', 'w', 'c', 3, $a), []];
        $f['HForm::label'] = [fn (array $a) => HForm::label('L', 'f', 'c', $a), []];
        $f['HForm::checkBox'] = [fn (array $a) => HForm::checkBox('n', 'L', 'v', false, false, 'c', $a), ['id']];
        $f['HForm::checkBox angehakt'] = [fn (array $a) => HForm::checkBox('n', 'L', 'v', true, true, 'c', $a), ['id']];
        $f['HForm::radioBox'] = [fn (array $a) => HForm::radioBox('i', 'g', 'L', 'v', false, false, 'c', $a), []];
        $f['HForm::radioBox angehakt'] = [fn (array $a) => HForm::radioBox('i', 'g', 'L', 'v', true, true, 'c', $a), []];
        $f['HForm::comboBox'] = [fn (array $a) => HForm::comboBox('n', [], 0, 'Bitte', 'c', $a), []];
        $f['HForm::listBox'] = [fn (array $a) => HForm::listBox('n', [], 1, 5, 'c', $a), []];
        $f['HForm::comboBoxArray'] = [fn (array $a) => HForm::comboBoxArray('n', [[1, 'Eins']], 1, 'Bitte', 'c', $a), []];
        $f['HForm::listBoxArray'] = [fn (array $a) => HForm::listBoxArray('n', [[1, 'Eins']], 1, 5, 'c', $a), []];
        $f['HForm::hGroupStart'] = [fn (array $a) => HForm::hGroupStart('c', $a), []];

        return $f;
    }

    /**
     * Referenzausgabe von main (Stand vor der Änderung), für harmlose Werte.
     * Bei delete ist der onclick ausgenommen (AK5) und wird vor dem Vergleich entfernt.
     *
     * @return array<string, array{0: callable, 1: string}>
     */
    public static function referenzen(): array
    {
        $a = ['data-x' => '1', 'aria-describedby' => 'y'];

        return [
            'project' => [fn () => HButton::project('/p', 'Text', 'Tipp', 'cls', $a), '<a href="/p" class="btn btn-create  cls" data-toggle="tooltip" title="Tipp" data-x="1" aria-describedby="y"  ><i class="fas fa-parking"></i> Text</a>'],
            'create' => [fn () => HButton::create('/p', 'Text', 'Tipp', 'cls', $a), '<a href="/p" class="btn btn-create  cls" data-toggle="tooltip" title="Tipp" data-x="1" aria-describedby="y" ><i class="fas fa-plus-circle"></i> Text</a>'],
            'show' => [fn () => HButton::show('/p', 'Text', 'Tipp', 'cls', $a), '<a href="/p" class="btn btn-show cls" data-toggle="tooltip" title="Tipp" data-x="1" aria-describedby="y" ><i class="fas fa-file-contract"></i> Text</a>'],
            'edit' => [fn () => HButton::edit('/p', 'Text', 'Tipp', 'cls', $a), '<a href="/p" data-toggle="tooltip" title="Tipp" class="btn btn-edit" data-x="1" aria-describedby="y" ><i class="fas fa-pencil-alt"></i> Text</a>'],
            'delete' => [fn () => HButton::delete(42, 'Text', 'Tipp', 'cls', $a), '<button data-kit-delete="42" data-toggle="tooltip" title="Tipp" class="btn btn-delete cls" data-x="1" aria-describedby="y" ><i class="fas fa-minus-circle"></i> Text</button>'."\n"],
            'submit' => [fn () => HButton::submit('Text', 'Tipp', 'cls', $a), '<button type="submit" class="btn btn-submit cls" data-x="1" aria-describedby="y"  data-toggle="tooltip" title="Tipp">Text</button>'],
            'save' => [fn () => HButton::save('Text', 'Tipp', 'cls', $a), '<button type="submit" class="btn btn-save cls" data-toggle="tooltip" title="Tipp" data-x="1" aria-describedby="y" ><i class="fas fa-save"></i> Text</button>'],
            'back' => [fn () => HButton::back('/p', 'Text', 'Tipp', 'cls', $a), '<a href="/p" class="btn btn-back cls" data-toggle="tooltip" title="Tipp" data-x="1" aria-describedby="y" ><i class="fas fa-undo"></i> Text</a>'],
            'project Vorgaben' => [fn () => HButton::project(), '<a href="#" class="btn btn-create  " data-toggle="tooltip" title="Projekte"  ><i class="fas fa-parking"></i> Projete</a>'],
            'create Vorgaben' => [fn () => HButton::create(), '<a href="#" class="btn btn-create  " data-toggle="tooltip" title="Erstellen" ><i class="fas fa-plus-circle"></i> Erstellen</a>'],
            'show Vorgaben' => [fn () => HButton::show(), '<a href="#" class="btn btn-show " data-toggle="tooltip" title="Anzeigen" ><i class="fas fa-file-contract"></i> Anzeigen</a>'],
            'edit Vorgaben' => [fn () => HButton::edit(), '<a href="#" data-toggle="tooltip" title="Bearbeiten" class="btn btn-edit" ><i class="fas fa-pencil-alt"></i> Bearbeiten</a>'],
            'delete Vorgaben' => [fn () => HButton::delete(7), '<button data-kit-delete="7" data-toggle="tooltip" title="L&ouml;schen" class="btn btn-delete " ><i class="fas fa-minus-circle"></i> L&ouml;schen</button>'."\n"],
            'submit Vorgaben' => [fn () => HButton::submit(), '<button type="submit" class="btn btn-submit "  data-toggle="tooltip" title="Speichern">Speichern</button>'],
            'save Vorgaben' => [fn () => HButton::save(), '<button type="submit" class="btn btn-save " data-toggle="tooltip" title="Speichern" ><i class="fas fa-save"></i> Speichern</button>'],
            'back Vorgaben' => [fn () => HButton::back(), '<a href="#" class="btn btn-back " data-toggle="tooltip" title="Zur&uuml;ck" ><i class="fas fa-undo"></i> Zur&uuml;ck</a>'],
        ];
    }

    /**
     * Ids für delete: Zahl, Zeichenketten und Angriffe auf das onclick-JavaScript.
     *
     * @return array<string, array{0: int|string}>
     */
    public static function ids(): array
    {
        return [
            'Zahl' => [42],
            'Ziffernstring' => ['17'],
            'Text' => ['abc-1'],
            'Anführungszeichen' => ['"><script>alert(\'x\')</script>&'],
            'JavaScript ausbrechen' => ["');alert(1);//"],
            'JavaScript ausbrechen doppelt' => ['");alert(1);//'],
            'Schrägstriche' => ['\\\\</script>'],
            'Zeilenumbruch' => ["a\nb"],
            'Zeilentrenner' => ["a\u{2028}b\u{2029}c"],
            'ohne Klammern' => ['1)+alert(2)+('],
            'Leerstring' => [''],
        ];
    }

    // ---------------------------------------------------------------- AK1

    // AK1
    #[DataProvider('attributParameter')]
    public function test_wert_steht_escaped_im_attribut_und_erzeugt_nichts_zusaetzliches(callable $aufruf, string $parameter, string $attribut): void
    {
        $platzhalter = 'PLATZHALTER';
        $baue = function (string $wert) use ($aufruf, $parameter): string {
            return match ($parameter) {
                'tooltip' => (string) $aufruf('Text', $wert),
                'class' => (string) $aufruf('Text', 'Tipp', $wert),
                'route' => (string) $aufruf('Text', 'Tipp', 'cls', $wert),
                'attrs' => (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-x' => $wert]),
            };
        };

        $basis = $baue($platzhalter);
        $vorlage = $this->wurzel($basis)->getAttribute($attribut);
        $this->assertStringContainsString($platzhalter, $vorlage, 'Aufbau: Attribut '.$attribut.' trägt den Wert in '.$basis);

        foreach (self::ZEICHEN as $wert) {
            $html = $baue($wert);

            $this->assertSame($this->struktur($basis), $this->struktur($html), 'zusätzliche Attribute oder Elemente bei '.$wert.': '.$html);
            $this->assertStringContainsString(e($wert), $html, 'nicht wie e() escaped: '.$html);
            $this->assertStringNotContainsString('<script', $html);
            $this->assertSame(str_replace($platzhalter, $wert, $vorlage), $this->wurzel($html)->getAttribute($attribut), 'Attributwert '.$attribut.' bei '.$wert.': '.$html);
        }
    }

    // AK1
    #[DataProvider('ids')]
    public function test_delete_id_erzeugt_keine_zusaetzlichen_attribute_oder_elemente(int|string $id): void
    {
        $basis = (string) HButton::delete(42, 'Text', 'Tipp', 'cls', ['data-x' => '1']);
        $html = (string) HButton::delete($id, 'Text', 'Tipp', 'cls', ['data-x' => '1']);

        $this->assertSame($this->struktur($basis), $this->struktur($html), $html);
        $this->assertSame(1, $this->dom($html)->query('//button')->length, $html);
        $this->assertSame(0, $this->dom($html)->query('//script')->length, $html);
        $this->assertSame((string) $id, $this->wurzel($html)->getAttribute('data-kit-delete'), 'Randfall 2: data-kit-delete wie bisher');
        $this->assertStringContainsString('data-kit-delete="'.e((string) $id).'"', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    // AK1
    public function test_edit_ignoriert_class_und_erzeugt_dabei_nichts(): void
    {
        $basis = (string) HButton::edit('/p', 'Text', 'Tipp', '', ['data-x' => '1']);

        foreach (self::ZEICHEN as $wert) {
            $html = (string) HButton::edit('/p', 'Text', 'Tipp', $wert, ['data-x' => '1']);
            $this->assertSame($this->struktur($basis), $this->struktur($html), $html);
            $this->assertStringNotContainsString('<script', $html);
        }
    }

    // ---------------------------------------------------------------- AK2

    // AK2
    #[DataProvider('methoden')]
    public function test_text_steht_escaped_im_button(callable $aufruf): void
    {
        $basis = (string) $aufruf('PLATZHALTER');

        foreach (['<', '>', '&', self::TEXT_PAYLOAD, self::PAYLOAD] as $text) {
            $html = (string) $aufruf($text);

            $this->assertSame($this->struktur($basis), $this->struktur($html), 'zusätzliche Elemente bei '.$text.': '.$html);
            $this->assertStringContainsString(e($text), $html);
            $this->assertSame($text, trim($this->wurzel($html)->textContent), $html);
        }
    }

    // AK2
    #[DataProvider('methoden')]
    public function test_htmlable_als_text_bleibt_unveraendert(callable $aufruf): void
    {
        $basis = (string) $aufruf('PLATZHALTER');
        $html = (string) $aufruf(new HtmlString('<b class="x">fett</b> &amp; <i>y</i>'));

        $this->assertStringContainsString('<b class="x">fett</b> &amp; <i>y</i>', $html);
        $this->assertSame(1, $this->dom($html)->query('//b[@class="x"]')->length, $html);
        $this->assertSame(1, $this->dom($html)->query('//i[not(@class)]')->length, $html);
        $this->assertSame(count($this->struktur($basis)) + 2, count($this->struktur($html)));
    }

    // AK2
    public function test_eigene_htmlable_implementierung_bleibt_unveraendert(): void
    {
        $text = new class implements Htmlable
        {
            public function toHtml(): string
            {
                return '<em>wichtig</em>';
            }
        };

        $this->assertStringContainsString('<em>wichtig</em>', (string) HButton::show('/a', $text));
        $this->assertStringContainsString('<em>wichtig</em>', (string) HButton::delete(1, $text));
        $this->assertStringContainsString('<em>wichtig</em>', (string) HButton::submit($text));
    }

    // ---------------------------------------------------------------- AK3

    // AK3
    public function test_vorgabetexte_zeigen_im_browser_dieselben_texte_wie_bisher(): void
    {
        $faelle = [
            [HButton::project(), 'Projete', 'Projekte'],
            [HButton::create(), 'Erstellen', 'Erstellen'],
            [HButton::show(), 'Anzeigen', 'Anzeigen'],
            [HButton::edit(), 'Bearbeiten', 'Bearbeiten'],
            [HButton::delete(3), 'Löschen', 'Löschen'],
            [HButton::submit(), 'Speichern', 'Speichern'],
            [HButton::save(), 'Speichern', 'Speichern'],
            [HButton::back(), 'Zurück', 'Zurück'],
        ];

        foreach ($faelle as [$button, $text, $tooltip]) {
            $html = (string) $button;
            $element = $this->wurzel($html);

            $this->assertSame($text, trim($element->textContent), $html);
            $this->assertSame($tooltip, $element->getAttribute('title'), $html);
            $this->assertStringNotContainsString('&amp;ouml;', $html);
            $this->assertStringNotContainsString('&amp;uuml;', $html);
            $this->assertStringNotContainsString('&amp;amp;', $html);
        }
    }

    // ---------------------------------------------------------------- AK4

    // AK4
    #[DataProvider('referenzen')]
    public function test_harmlose_werte_ergeben_dieselbe_ausgabe_wie_vorher(callable $render, string $erwartet): void
    {
        $this->assertSame($erwartet, $this->ohneOnclick((string) $render()));
    }

    // ---------------------------------------------------------------- AK5

    // AK5 / Randfall 2
    #[DataProvider('ids')]
    public function test_klick_ohne_globale_funktion_wirft_keinen_fehler(int|string $id): void
    {
        $onclick = $this->wurzel((string) HButton::delete($id))->getAttribute('onclick');

        $this->assertNotSame('', $onclick, 'Gegenprobe: onclick ist da');
        $ergebnis = $this->klick($onclick, false);

        $this->assertNull($ergebnis['error'], 'onclick: '.$onclick);
        $this->assertSame([], $ergebnis['alerts']);
    }

    // AK5 / Randfall 2
    #[DataProvider('ids')]
    public function test_klick_mit_globaler_funktion_ruft_sie_mit_der_id_auf(int|string $id): void
    {
        $onclick = $this->wurzel((string) HButton::delete($id))->getAttribute('onclick');

        $ergebnis = $this->klick($onclick, true);

        $this->assertNull($ergebnis['error'], 'onclick: '.$onclick);
        $this->assertCount(1, $ergebnis['calls'], 'onclick: '.$onclick);
        $this->assertCount(1, $ergebnis['calls'][0], 'onclick: '.$onclick);
        $this->assertSame((string) $id, (string) $ergebnis['calls'][0][0], 'onclick: '.$onclick);
        $this->assertSame([], $ergebnis['alerts'], 'fremder Code lief: '.$onclick);
    }

    // AK5
    public function test_klick_ruft_mit_zahl_die_id_als_zahl_oder_zeichenkette_auf_ohne_weiteres(): void
    {
        $html = (string) HButton::delete(42);
        $onclick = $this->wurzel($html)->getAttribute('onclick');

        $this->assertSame(42, (int) $this->klick($onclick, true)['calls'][0][0]);
        $this->assertSame('42', $this->wurzel($html)->getAttribute('data-kit-delete'), 'data-kit-delete wie bisher');
    }

    // ---------------------------------------------------------------- AK6

    /**
     * Fest gesetzte Attributnamen des Elements, das die $attributes bekommt (aus der Ausgabe ohne Zusatz).
     *
     * @return list<string>
     */
    private function festeNamen(callable $aufruf, string $basis): array
    {
        $this->assertStringContainsString('data-x="1"', $basis, 'Aufbau: Attribute werden ausgegeben');
        $probe = null;
        foreach ($this->dom($basis)->query('//*[@data-x]') as $element) {
            $probe = $element;
            break;
        }
        $this->assertNotNull($probe, 'Aufbau: Element mit data-x in '.$basis);
        $fest = [];
        foreach ($probe->attributes as $attribut) {
            if ($attribut->name !== 'data-x') {
                $fest[] = $attribut->name;
            }
        }
        $this->assertNotEmpty($fest, 'Aufbau: feste Attribute in '.$basis);

        return $fest;
    }

    // AK6
    #[DataProvider('attributAufrufe')]
    public function test_attribut_mit_festem_namen_wird_nicht_ausgegeben(callable $aufruf, array $ausnahmen): void
    {
        $basis = (string) $aufruf(['data-x' => '1']);

        foreach (array_diff($this->festeNamen($aufruf, $basis), $ausnahmen) as $name) {
            foreach ([$name, strtoupper($name), ucfirst($name)] as $schluessel) {
                $html = (string) $aufruf(['data-x' => '1', $schluessel => 'FREMD_WERT']);

                $this->assertStringNotContainsString('FREMD_WERT', $html, 'Paar '.$schluessel.' wurde ausgegeben: '.$html);
                $this->keinAttributDoppelt($html);
                $this->assertSame($this->glatt($basis), $this->glatt($html), 'Schlüssel '.$schluessel.' verändert die Ausgabe');
            }
        }
    }

    // AK6
    #[DataProvider('attributAufrufe')]
    public function test_alle_festen_namen_zusammen_ergeben_kein_doppeltes_attribut(callable $aufruf, array $ausnahmen): void
    {
        $basis = (string) $aufruf(['data-x' => '1']);
        $attribute = ['data-x' => '1'];
        foreach (array_diff($this->festeNamen($aufruf, $basis), $ausnahmen) as $name) {
            foreach ([$name, strtoupper($name), ucfirst($name)] as $schluessel) {
                $attribute[$schluessel] = 'FREMD_WERT';
            }
        }

        $html = (string) $aufruf($attribute);

        $this->keinAttributDoppelt($html);
        $this->assertStringNotContainsString('FREMD_WERT', $html);
        $this->assertSame($this->glatt($basis), $this->glatt($html));
    }

    // AK6
    public function test_label_gibt_for_und_class_nicht_doppelt_aus(): void
    {
        $html = (string) HForm::label('Text', 'fid', 'cls', ['for' => 'anders', 'CLASS' => 'fremd', 'Data-X' => '1']);

        $this->keinAttributDoppelt($html);
        $element = $this->wurzel($html);
        $this->assertSame('fid', $element->getAttribute('for'));
        $this->assertSame('form-check-label cls', $element->getAttribute('class'));
        // einzelwort: Testwert kommt in der Ausgabe sonst nie vor (nur als fremder Attributwert)
        $this->assertStringNotContainsString('anders', $html);
        // einzelwort: Testwert kommt in der Ausgabe sonst nie vor (nur als fremder Attributwert)
        $this->assertStringNotContainsString('fremd', $html);
        $this->assertSame('1', $element->getAttribute('data-x'), 'Gegenprobe: freie Schlüssel bleiben');
    }

    // AK6
    #[DataProvider('methoden')]
    public function test_hbutton_gibt_fest_gesetzte_attribute_nicht_doppelt_aus(callable $aufruf): void
    {
        $html = (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-x' => '1', 'ID' => 'frei', 'Class' => 'FREMD', 'TITLE' => 'FREMD', 'data-TOGGLE' => 'FREMD'], 5);

        $this->keinAttributDoppelt($html);
        // einzelwort: Testwert kommt in der Ausgabe sonst nie vor (nur als fremder Attributwert)
        $this->assertStringNotContainsString('FREMD', $html);
        $this->assertSame('frei', $this->wurzel($html)->getAttribute('id'), 'Gegenprobe: nicht fest gesetzte Schlüssel bleiben');
    }

    // AK6 (Ausnahme)
    #[DataProvider('idSchreibweisen')]
    public function test_checkbox_uebernimmt_id_auch_in_anderer_schreibweise(string $schluessel): void
    {
        $html = (string) HForm::checkBox('name1', 'Beschriftung', 'v', false, false, '', [$schluessel => 'meine-id', 'data-x' => '1']);

        $this->keinAttributDoppelt($html);
        $xpath = $this->dom($html);
        $this->assertSame('meine-id', $xpath->evaluate('string(//input/@id)'), $html);
        $this->assertSame('meine-id', $xpath->evaluate('string(//label/@for)'), $html);
        $this->assertSame('1', $xpath->evaluate('string(//input/@data-x)'), 'Gegenprobe: weitere Attribute bleiben');
        $this->assertSame(1, substr_count(strtolower($html), 'id="meine-id"'), $html);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function idSchreibweisen(): array
    {
        return ['id' => ['id'], 'ID' => ['ID'], 'Id' => ['Id'], 'iD' => ['iD']];
    }

    // ---------------------------------------------------------------- Randfälle

    // Randfall 1
    #[DataProvider('methoden')]
    public function test_ungueltige_attributschluessel_entfallen_in_hbutton(callable $aufruf): void
    {
        $basis = (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-ok' => '1', 'aria-describedby' => 'y'], 5);
        $this->assertStringContainsString('data-ok="1"', $basis, 'Aufbau: gültige Schlüssel erscheinen');

        foreach (['a b', 'a"b', "a'b", 'a>b', 'a/b', 'a=b', ' ', '', "data-a\n", ' data-a', "data-a\t", "\ndata-a", "data-a\r\n"] as $schluessel) {
            $html = (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-ok' => '1', $schluessel => 'WERT_ZZ', 'aria-describedby' => 'y'], 5);

            $this->assertStringNotContainsString('WERT_ZZ', $html, 'Paar mit Schlüssel "'.$schluessel.'" wurde ausgegeben: '.$html);
            $this->assertSame($this->struktur($basis), $this->struktur($html), 'Schlüssel "'.$schluessel.'" erzeugt zusätzliche Attribute: '.$html);
            $this->assertStringContainsString('data-ok="1"', $html);
            $this->assertStringContainsString('aria-describedby="y"', $html);
        }
    }

    // Randfall 3
    #[DataProvider('methoden')]
    public function test_null_ergibt_leeren_string_wie_bisher(callable $aufruf): void
    {
        $this->assertSame((string) $aufruf('', 'Tipp', 'cls', '/p'), (string) $aufruf(null, 'Tipp', 'cls', '/p'), 'text');
        $this->assertSame((string) $aufruf('Text', '', 'cls', '/p'), (string) $aufruf('Text', null, 'cls', '/p'), 'tooltip');
        $this->assertSame((string) $aufruf('Text', 'Tipp', '', '/p'), (string) $aufruf('Text', 'Tipp', null, '/p'), 'class');
        $this->assertSame((string) $aufruf('Text', 'Tipp', 'cls', ''), (string) $aufruf('Text', 'Tipp', 'cls', null), 'route');
        $this->assertSame(
            (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-x' => '']),
            (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-x' => null]),
            'attributwert'
        );
        $this->assertStringContainsString('data-x=""', (string) $aufruf('Text', 'Tipp', 'cls', '/p', ['data-x' => null]), 'Gegenprobe: Attribut bleibt mit leerem Wert');
    }

    // Randfall 3
    public function test_delete_mit_null_als_id_hat_leeres_data_kit_delete(): void
    {
        $html = (string) HButton::delete(null);

        $this->assertSame('', $this->wurzel($html)->getAttribute('data-kit-delete'), $html);
        $this->assertStringContainsString('data-kit-delete=""', $html);
    }

    // ---------------------------------------------------------------- AK7

    // AK7
    public function test_readme_beschreibt_escaping_von_hbutton(): void
    {
        $readme = (string) file_get_contents(dirname(__DIR__, 2).'/README.md');
        $this->assertNotSame('', $readme, 'Aufbau: README gelesen');

        $this->assertSame(1, preg_match('/^###+ [^\n]*HButton[^\n]*\n(.*?)(?=^##|\z)/ms', $readme, $m), 'Abschnitt zu HButton fehlt');
        $abschnitt = $m[1];

        $this->assertStringContainsString('Verhaltensänderung', $readme);
        $this->assertMatchesRegularExpression('/escap/i', $abschnitt);
        $this->assertStringContainsString('HtmlString', $abschnitt);
        $this->assertMatchesRegularExpression('/doppel/i', $abschnitt);
        $this->assertStringContainsString('$attributes', $abschnitt);
    }
}
