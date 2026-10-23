<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Support\HtmlString;
use ITHilbert\LaravelKit\Helpers\HButton;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Charakterisierung der HTML-Buttons (Ist-Stand).
 */
final class HButtonTest extends TestCase
{
    // Charakterisierung
    public function test_create_liefert_html_string_mit_route_und_standardtext(): void
    {
        $button = HButton::create('/neu');

        $this->assertInstanceOf(HtmlString::class, $button);
        $this->assertStringContainsString('href="/neu"', (string) $button);
        $this->assertStringContainsString('btn btn-create', (string) $button);
        $this->assertStringContainsString('fa-plus-circle', (string) $button);
        $this->assertStringContainsString('Erstellen</a>', (string) $button);
    }

    // Charakterisierung
    public function test_create_ohne_argumente_verweist_auf_raute(): void
    {
        $this->assertStringContainsString('href="#"', (string) HButton::create());
    }

    // Charakterisierung
    public function test_show_und_edit_und_back_haben_ihre_klassen_und_standardtexte(): void
    {
        $show = (string) HButton::show('/a');
        $edit = (string) HButton::edit('/b');
        $back = (string) HButton::back('/c');

        $this->assertStringContainsString('btn btn-show', $show);
        $this->assertStringContainsString('Anzeigen</a>', $show);
        $this->assertStringContainsString('btn btn-edit', $edit);
        $this->assertStringContainsString('Bearbeiten</a>', $edit);
        $this->assertStringContainsString('btn btn-back', $back);
        $this->assertStringContainsString('Zur&uuml;ck</a>', $back);
    }

    // Charakterisierung
    public function test_delete_ruft_open_delete_form_mit_id_auf(): void
    {
        $html = (string) HButton::delete(42);

        $this->assertStringContainsString('onclick="OpenDeleteForm(42)"', $html);
        $this->assertStringContainsString('btn btn-delete', $html);
        $this->assertStringContainsString('L&ouml;schen</button>', $html);
    }

    // Charakterisierung
    public function test_submit_und_save_sind_submit_buttons(): void
    {
        $submit = (string) HButton::submit();
        $save = (string) HButton::save();

        $this->assertStringContainsString('type="submit" class="btn btn-submit', $submit);
        $this->assertStringContainsString('>Speichern</button>', $submit);
        $this->assertStringContainsString('type="submit" class="btn btn-save', $save);
        $this->assertStringContainsString('fa-save', $save);
    }

    // Charakterisierung
    public function test_zusaetzliche_klassen_und_attribute_werden_ausgegeben(): void
    {
        $html = (string) HButton::create('/x', 'Neu', 'Tipp', 'extra-klasse', ['id' => 'btn1', 'data-x' => 'y']);

        $this->assertStringContainsString('extra-klasse', $html);
        $this->assertStringContainsString('title="Tipp"', $html);
        $this->assertStringContainsString('id="btn1"', $html);
        $this->assertStringContainsString('data-x="y"', $html);
        $this->assertStringContainsString('Neu</a>', $html);
    }

    // Charakterisierung: Der Button ist ein HtmlString, daher in Blade unescaped ausgegeben
    public function test_text_wird_nicht_escaped(): void
    {
        $this->assertStringContainsString('<b>fett</b>', (string) HButton::show('/a', '<b>fett</b>'));
    }

    // Charakterisierung: edit() ignoriert den Parameter $class (heutiges Verhalten)
    public function test_edit_ignoriert_die_uebergebene_klasse(): void
    {
        $this->assertStringNotContainsString('meine-klasse', (string) HButton::edit('/a', 'E', 'T', 'meine-klasse'));
    }
}
