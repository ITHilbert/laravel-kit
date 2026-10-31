<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Kit-Component combobox in beiden Themes. Vorlage: vue/tailwind/forms/combobox.vue und
 * vue/bootstrap5/forms/combobox.vue.
 *
 * Vertrag `options`: Liste von Einträgen mit `cbKey` (Wert) und `cbCaption` (Text) wie in der Vue-Version
 * — als Array von Arrays, als Collection/Array von Objekten oder als JSON-Text dieser Liste
 * (so liefert ihn `getComboBoxData()`). Nicht gesetzt oder leer: keine Optionen. `value` wird als Text mit
 * `cbKey` verglichen.
 */
final class ComboboxTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function themes(): array
    {
        return ['tailwind' => ['tailwind'], 'bootstrap5' => ['bootstrap5']];
    }

    /** @return list<array{cbKey: int, cbCaption: string}> */
    private function rollen(): array
    {
        return [
            ['cbKey' => 1, 'cbCaption' => 'Admin'],
            ['cbKey' => 2, 'cbCaption' => 'Manager'],
            ['cbKey' => 3, 'cbCaption' => 'User'],
        ];
    }

    private function rendern(string $theme, string $blade, array $daten = []): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade, $daten);
    }

    private function dom(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>');
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function select(string $html): DOMElement
    {
        $knoten = $this->dom($html)->query('//select');
        $this->assertSame(1, $knoten->length, "Genau ein <select> erwartet:\n{$html}");

        return $knoten->item(0);
    }

    /** @return list<array{string, string, bool}> [Wert, Text, ausgewählt] */
    private function optionen(string $html): array
    {
        $liste = [];
        foreach ($this->dom($html)->query('//select/option') as $option) {
            $liste[] = [$option->getAttribute('value'), trim($option->textContent), $option->hasAttribute('selected')];
        }

        return $liste;
    }

    // AK6
    #[DataProvider('themes')]
    public function test_gibt_select_mit_name_aus(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" />', ['o' => $this->rollen()]);

        $this->assertSame('role_id', $this->select($html)->getAttribute('name'));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_gibt_alle_optionen_mit_wert_und_text_aus(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" />', ['o' => $this->rollen()]);

        $this->assertSame(
            [['1', 'Admin', false], ['2', 'Manager', false], ['3', 'User', false]],
            $this->optionen($html),
        );
    }

    // AK6
    #[DataProvider('themes')]
    public function test_nimmt_options_als_json_text_wie_get_combo_box_data(string $theme): void
    {
        $json = json_encode($this->rollen());
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" value="2" />', ['o' => $json]);

        $this->assertSame(
            [['1', 'Admin', false], ['2', 'Manager', true], ['3', 'User', false]],
            $this->optionen($html),
        );
    }

    // AK6
    #[DataProvider('themes')]
    public function test_nimmt_options_als_collection_von_objekten(string $theme): void
    {
        $o = collect($this->rollen())->map(fn (array $r) => (object) $r);
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" />', ['o' => $o]);

        $this->assertSame(
            [['1', 'Admin', false], ['2', 'Manager', false], ['3', 'User', false]],
            $this->optionen($html),
        );
    }

    // AK6
    #[DataProvider('themes')]
    public function test_nur_die_option_mit_value_ist_ausgewaehlt(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" value="3" />', ['o' => $this->rollen()]);

        $this->assertSame(
            [['1', 'Admin', false], ['2', 'Manager', false], ['3', 'User', true]],
            $this->optionen($html),
        );
    }

    // AK6
    #[DataProvider('themes')]
    public function test_value_aus_dem_aufruf_mit_old_waehlt_die_option(string $theme): void
    {
        $html = $this->rendern(
            $theme,
            '<x-kit::combobox name="role_id" :options="$o" value="{{ old(\'role_id\', 3) }}" required />',
            ['o' => $this->rollen()],
        );

        $this->assertSame([false, false, true], array_column($this->optionen($html), 2));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_ohne_value_und_mit_unbekanntem_value_ist_keine_option_ausgewaehlt(string $theme): void
    {
        $ohne = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" />', ['o' => $this->rollen()]);
        $unbekannt = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" value="99" />', ['o' => $this->rollen()]);

        $this->assertSame(3, count($this->optionen($ohne)));
        $this->assertSame([false, false, false], array_column($this->optionen($ohne), 2));
        $this->assertSame(3, count($this->optionen($unbekannt)));
        $this->assertSame([false, false, false], array_column($this->optionen($unbekannt), 2));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_required_landet_am_select(string $theme): void
    {
        $mit = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" required />', ['o' => $this->rollen()]);
        $ohne = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" />', ['o' => $this->rollen()]);

        $this->assertTrue($this->select($mit)->hasAttribute('required'));
        $this->assertFalse($this->select($ohne)->hasAttribute('required'));
    }

    // AK6
    public function test_tailwind_select_hat_klasse_combobox_und_nimmt_weitere_klassen_an(): void
    {
        $html = $this->rendern('tailwind', '<x-kit::combobox name="r" :options="$o" class="meine" id="rolle" data-x="y" />', ['o' => $this->rollen()]);
        $select = $this->select($html);

        $klassen = preg_split('/\s+/', trim($select->getAttribute('class')));
        $this->assertContains('combobox', $klassen);
        $this->assertContains('meine', $klassen);
        $this->assertSame('rolle', $select->getAttribute('id'));
        $this->assertSame('y', $select->getAttribute('data-x'));
    }

    // AK6
    public function test_bootstrap5_select_hat_klassen_form_control_combobox_und_add_class(): void
    {
        $html = $this->rendern('bootstrap5', '<x-kit::combobox name="r" :options="$o" add-class="mb-3" class="meine" id="rolle" data-x="y" />', ['o' => $this->rollen()]);
        $select = $this->select($html);

        $klassen = preg_split('/\s+/', trim($select->getAttribute('class')));
        $this->assertContains('form-control', $klassen);
        $this->assertContains('combobox', $klassen);
        $this->assertContains('mb-3', $klassen);
        $this->assertContains('meine', $klassen);
        $this->assertSame('rolle', $select->getAttribute('id'));
        $this->assertSame('y', $select->getAttribute('data-x'));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_slot_steht_im_select_vor_den_optionen(string $theme): void
    {
        $html = $this->rendern(
            $theme,
            '<x-kit::combobox name="r" :options="$o"><option value="">Bitte wählen</option></x-kit::combobox>',
            ['o' => $this->rollen()],
        );

        $optionen = $this->optionen($html);
        $this->assertCount(4, $optionen);
        $this->assertSame(['', 'Bitte wählen', false], $optionen[0]);
        $this->assertSame('1', $optionen[1][0]);
    }

    // AK6
    #[DataProvider('themes')]
    public function test_select_traegt_options_nicht_als_attribut(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" value="2" />', ['o' => $this->rollen()]);

        $select = $this->select($html);
        $this->assertFalse($select->hasAttribute('options'));
        $this->assertFalse($select->hasAttribute('cbkey'));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_optionstexte_sind_escaped(string $theme): void
    {
        $o = [['cbKey' => 1, 'cbCaption' => '<script>alert(1)</script><b>fett</b> & "q"']];
        $html = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" />', ['o' => $o]);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertSame('<script>alert(1)</script><b>fett</b> & "q"', $this->optionen($html)[0][1]);
    }

    // AK6
    #[DataProvider('themes')]
    public function test_optionswerte_und_value_sind_escaped(string $theme): void
    {
        $o = [['cbKey' => '"><script>x()</script>', 'cbCaption' => 'A']];
        $html = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" :value="$v" />', ['o' => $o, 'v' => '"><script>x()</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $optionen = $this->optionen($html);
        $this->assertCount(1, $optionen);
        $this->assertSame('"><script>x()</script>', $optionen[0][0]);
        $this->assertTrue($optionen[0][2]);
    }

    // AK6
    #[DataProvider('themes')]
    public function test_ausdruck_in_optionstext_name_und_attribut_erscheint_als_text(string $theme): void
    {
        $o = [['cbKey' => 1, 'cbCaption' => '{{ 7*7 }}']];
        $html = $this->rendern($theme, '<x-kit::combobox :name="$n" :options="$o" :data-x="$n" :title="$n" />', ['o' => $o, 'n' => '{{ 7*7 }}']);

        $select = $this->select($html);
        $this->assertSame('{{ 7*7 }}', $select->getAttribute('name'));
        $this->assertSame('{{ 7*7 }}', $select->getAttribute('data-x'));
        $this->assertSame('{{ 7*7 }}', $select->getAttribute('title'));
        $this->assertSame('{{ 7*7 }}', $this->optionen($html)[0][1]);
        $this->assertStringNotContainsString('49', $html);
    }

    // AK6
    #[DataProvider('themes')]
    public function test_html_im_attribut_wird_escaped(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" :title="$t" />', ['o' => $this->rollen(), 't' => '"><script>x()</script>']);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertSame('"><script>x()</script>', $this->select($html)->getAttribute('title'));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_erzeugt_keine_vue_tags_und_keine_vue_attribute(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" value="2" required />', ['o' => $this->rollen()]);

        $this->assertDoesNotMatchRegularExpression('/<combobox[\s>\/]/i', $html);
        $this->assertDoesNotMatchRegularExpression('/<[a-z][a-z0-9]*-[a-z0-9-]*[\s>\/]/i', $html);
        $this->assertDoesNotMatchRegularExpression('/\sv-[\w:.\-]*(?=[=\s>\/])/i', $html);
        $this->assertDoesNotMatchRegularExpression('/\s:[\w.\-]+(?=[=\s>\/])/i', $html);
        $this->assertStringNotContainsString('@click', $html);
    }

    // Randfall (Spec userauth-admin-views: leere Rollenliste in user/create)
    #[DataProvider('themes')]
    public function test_leere_optionsliste_gibt_select_ohne_optionen_ohne_fehler(string $theme): void
    {
        foreach ([[], '[]', collect()] as $leer) {
            $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" value="3" required />', ['o' => $leer]);

            $this->assertSame('role_id', $this->select($html)->getAttribute('name'));
            $this->assertSame([], $this->optionen($html));
        }
    }

    // Randfall (Spec userauth-admin-views: leere Rollenliste in user/create)
    #[DataProvider('themes')]
    public function test_ohne_options_gibt_select_ohne_optionen_ohne_fehler(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" required />');

        $this->assertSame('role_id', $this->select($html)->getAttribute('name'));
        $this->assertSame([], $this->optionen($html));
    }

    // Randfall (Spec userauth-admin-views: leere Rollenliste in user/create)
    #[DataProvider('themes')]
    public function test_options_als_leerer_text_gibt_select_ohne_optionen_ohne_fehler(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="role_id" :options="$o" />', ['o' => '']);

        $this->assertSame([], $this->optionen($html));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_value_leer_gesetzt_waehlt_keine_option(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::combobox name="r" :options="$o" value="" />', ['o' => $this->rollen()]);

        $this->assertSame([false, false, false], array_column($this->optionen($html), 2));
    }

    // AK6
    #[DataProvider('themes')]
    public function test_formular_submit_sendet_den_gewaehlten_wert(string $theme): void
    {
        $html = $this->rendern($theme, '<form method="post" action="/x"><x-kit::combobox name="role_id" :options="$o" value="2" required /></form>', ['o' => $this->rollen()]);

        $select = $this->select($html);
        $this->assertSame('role_id', $select->getAttribute('name'));
        $gewaehlt = array_values(array_filter($this->optionen($html), fn (array $o) => $o[2]));
        $this->assertSame([['2', 'Manager', true]], $gewaehlt);
    }
}
