<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use ITHilbert\LaravelKit\Kit\KitTheme;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Theme `kern` der Blade-Felder (Spec blade-kern, T1): Markup wie die Vue-Kern-Felder.
 */
final class KernFelderTest extends TestCase
{
    use KernHilfe;

    private const OPTIONEN = "['a' => 'Alpha', 'b' => 'Beta']";

    /** @return array<string, array{string, string, string, ?string}> Baustein, Blade, Wurzelklasse, input-type */
    public static function felder(): array
    {
        return [
            'input-text' => ['input-text', '<x-kit::input-text name="vorname" label="Vorname" />', 'kit-input-text', 'text'],
            'input-email' => ['input-email', '<x-kit::input-email name="vorname" label="Vorname" />', 'kit-input-email', 'email'],
            'input-password' => ['input-password', '<x-kit::input-password name="vorname" label="Vorname" />', 'kit-input-password', 'password'],
            'input-date' => ['input-date', '<x-kit::input-date name="vorname" label="Vorname" />', 'kit-input-date', 'date'],
            'input-number' => ['input-number', '<x-kit::input-number name="vorname" label="Vorname" />', 'kit-input-number', 'number'],
            'input-euro' => ['input-euro', '<x-kit::input-euro name="vorname" label="Vorname" />', 'kit-input-euro', 'number'],
            'text-area' => ['text-area', '<x-kit::text-area name="vorname" label="Vorname" />', 'kit-textarea', null],
            'select' => ['select', '<x-kit::select name="vorname" label="Vorname" :options="'.self::OPTIONEN.'" />', 'kit-select', null],
        ];
    }

    /** @return array<string, array{string}> Blade je Feld-Baustein (alle ausser input-hidden) */
    public static function alleFeldBlades(): array
    {
        $blades = array_map(static fn (array $f): array => [$f[1]], self::felder());
        $blades['input-file'] = ['<x-kit::input-file name="vorname" label="Vorname" />'];
        $blades['input-file-img'] = ['<x-kit::input-file-img name="vorname" />'];
        $blades['checkbox'] = ['<x-kit::checkbox name="vorname">Text</x-kit::checkbox>'];
        $blades['combobox'] = ['<x-kit::combobox name="vorname" label="Vorname" :options="'.self::OPTIONEN.'" />'];
        $blades['label'] = ['<x-kit::label for="vorname">Vorname</x-kit::label>'];

        return $blades;
    }

    private function fehlerTeilen(): void
    {
        config(['laravelkit.fehleranzeige' => true]);
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['vorname' => ['Vorname fehlt.']])));
    }

    // AK1
    public function test_theme_kern_ist_erlaubt_und_alle_feld_bausteine_rendern_kit_markup(): void
    {
        $this->assertContains('kern', KitTheme::erlaubteWerte());

        foreach (self::alleFeldBlades() as $name => [$blade]) {
            $html = $this->kernRendern($blade);
            $this->assertStringContainsString('kit-', $html, "{$name}: kein kit-Markup");
            // einzelwort: Kit-Bausteine rendern nur ihr eigenes Markup, form-control ist eine Bootstrap-Klasse
            $this->assertStringNotContainsString('form-control', $html, "{$name}: Bootstrap-Markup");
        }

        $hidden = $this->kernRendern('<x-kit::input-hidden name="h" value="1" />');
        $this->sieHat($hidden, "//input[@type='hidden'][@name='h'][@value='1']");
    }

    // AK2
    #[DataProvider('felder')]
    public function test_feld_hat_wurzel_label_und_eingabe_mit_kit_klassen(string $name, string $blade, string $wurzel, ?string $type): void
    {
        $html = $this->kernRendern($blade);

        $this->sieHat($html, "//div{$this->kl($wurzel)}/label{$this->kl($wurzel.'__label')}[@for='vorname']");
        $feld = "//div{$this->kl($wurzel)}//*{$this->kl($wurzel.'__input')}[@id='vorname'][@name='vorname']";
        $this->sieHat($html, $feld);
        if ($type !== null) {
            $this->sieHat($html, $feld."[@type='{$type}']");
        }
    }

    // AK3
    public function test_fehler_setzt_is_invalid_aria_und_zeigt_meldung_mit_old_wert(): void
    {
        // Gegenprobe: ohne Fehler weder is-invalid noch __error
        $ohne = $this->kernRendern('<x-kit::input-text name="vorname" />');
        $this->sieHat($ohne, "//div{$this->kl('kit-input-text')}//input{$this->kl('kit-input-text__input')}");
        $this->sieHatNicht($ohne, '//*[contains(@class, "is-invalid")]');
        $this->sieHatNicht($ohne, '//*[contains(@class, "kit-input-text__error")]');

        $this->fehlerTeilen();
        $session = $this->app['session']->driver();
        $session->put('_old_input', ['vorname' => 'Max']);
        $this->app['request']->setLaravelSession($session);
        $html = $this->kernRendern('<x-kit::input-text name="vorname" />');

        $this->sieHat($html, "//div{$this->kl('kit-input-text')}{$this->kl('is-invalid')}");
        $eingabe = "//input{$this->kl('kit-input-text__input')}";
        $this->assertSame('Max', $this->attribut($html, $eingabe, 'value'));
        $this->assertSame('true', $this->attribut($html, $eingabe, 'aria-invalid'));
        $fehlerId = $this->attribut($html, "//span{$this->kl('kit-input-text__error')}", 'id');
        $this->assertNotEmpty($fehlerId);
        $this->assertStringContainsString($fehlerId, (string) $this->attribut($html, $eingabe, 'aria-describedby'));
        $this->assertSame('Vorname fehlt.', $this->text($html, "//span{$this->kl('kit-input-text__error')}"));
    }

    // AK4
    public function test_input_euro_hat_suffix_nach_dem_eingabefeld(): void
    {
        $html = $this->kernRendern('<x-kit::input-euro name="preis" />');

        $this->sieHat($html, "//input{$this->kl('kit-input-euro__input')}/following-sibling::*{$this->kl('kit-input-euro__suffix')}[contains(., '€')]");
    }

    // AK5
    public function test_checkbox_ist_angehakt_nur_mit_checked_und_label_zeigt_auf_das_feld(): void
    {
        $mit = $this->kernRendern('<x-kit::checkbox name="aktiv" checked>Aktiv</x-kit::checkbox>');
        $ohne = $this->kernRendern('<x-kit::checkbox name="aktiv">Aktiv</x-kit::checkbox>');

        $feld = "//div{$this->kl('kit-checkbox')}/input{$this->kl('kit-checkbox__input')}[@type='checkbox'][@value='1']";
        $this->sieHat($mit, $feld.'[@checked]');
        $this->sieHat($ohne, $feld);
        $this->sieHatNicht($ohne, '//input[@checked]');
        $id = $this->attribut($mit, "//input{$this->kl('kit-checkbox__input')}", 'id');
        $this->assertNotEmpty($id);
        $this->sieHat($mit, "//label{$this->kl('kit-checkbox__label')}[@for='{$id}'][contains(., 'Aktiv')]");
    }

    // AK6
    public function test_input_file_img_hat_dateifeld_mit_bildtypen(): void
    {
        $html = $this->kernRendern('<x-kit::input-file-img name="bild" />');

        $this->sieHat($html, "//div{$this->kl('kit-input-file')}/input{$this->kl('kit-input-file__input')}[@type='file'][@accept='image/png, image/jpeg']");
    }

    // Randfall 1
    public function test_ohne_label_gibt_es_kein_label_element(): void
    {
        foreach (['input-text', 'text-area', 'input-file', 'select'] as $name) {
            $mit = $this->kernRendern("<x-kit::{$name} name=\"f\" label=\"Titel\" />");
            $ohne = $this->kernRendern("<x-kit::{$name} name=\"f\" />");

            $this->sieHat($mit, '//label[@for="f"]');
            $this->sieHatNicht($ohne, '//label');
            $this->sieHat($ohne, '//*[@name="f"]');
        }
    }

    // Randfall 2
    public function test_select_ohne_options_ist_ein_leeres_select(): void
    {
        $html = $this->kernRendern('<x-kit::select name="land" />');

        $this->sieHat($html, "//select{$this->kl('kit-select__input')}[@name='land']");
        $this->sieHatNicht($html, '//option');
    }

    // Randfall 4
    public function test_html_in_label_value_und_option_wird_escaped(): void
    {
        $html = $this->kernRendern('<x-kit::input-text name="f" label="<b>L</b>" value="<b>V</b>" />'
            .'<x-kit::select name="s" :options="[\'a\' => \'<b>O</b>\']" />');

        $this->assertStringContainsString('&lt;b&gt;L&lt;/b&gt;', $html);
        $this->assertStringContainsString('&lt;b&gt;O&lt;/b&gt;', $html);
        $this->assertSame('<b>V</b>', $this->attribut($html, '//input[@name="f"]', 'value'));
        $this->sieHatNicht($html, '//b');
    }

    // Randfall 5
    public function test_fehleranzeige_aus_zeigt_keinen_fehler_und_laesst_aria_describedby_unveraendert(): void
    {
        config(['laravelkit.fehleranzeige' => false]);
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag(['vorname' => ['Vorname fehlt.']])));
        $blade = '<x-kit::input-text name="vorname" aria-describedby="hilfe" />';

        $aus = $this->kernRendern($blade);
        config(['laravelkit.fehleranzeige' => true]);
        $an = $this->kernRendern($blade);

        $this->sieHat($an, '//*[contains(@class, "kit-input-text__error")]'); // Gegenprobe: Aufbau greift
        $this->sieHatNicht($aus, '//*[contains(@class, "kit-input-text__error")]');
        $this->sieHatNicht($aus, '//*[contains(@class, "is-invalid")]');
        $this->sieHatNicht($aus, '//*[@aria-invalid]');
        $this->assertSame('hilfe', $this->attribut($aus, '//input', 'aria-describedby'));
    }

    // AK14
    public function test_gerenderte_felder_tragen_nur_kit_klassen_und_hoechstens_eine_je_element(): void
    {
        // Gegenprobe: der Prüfer erkennt Fremdklassen und doppelte kit-Klassen
        $this->assertSame(['form-control'], $this->fremdeKlassen('<div class="form-control kit-a is-invalid"></div>'));
        $this->assertCount(1, $this->fremdeKlassen('<div class="kit-a kit-b"></div>'));

        $this->fehlerTeilen();
        foreach (self::alleFeldBlades() as $name => [$blade]) {
            $html = $this->kernRendern($blade);
            $this->assertSame([], $this->fremdeKlassen($html), "{$name}: {$html}");
            $this->sieHatNicht($html, '//i');
        }
    }
}
