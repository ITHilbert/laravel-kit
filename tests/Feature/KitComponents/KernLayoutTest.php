<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Kit\KitAnzeige;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Theme `kern` der Blade-Bausteine Buttons, Cards, show-euro, show-date, breadcrumb (Spec blade-kern, T2).
 */
final class KernLayoutTest extends TestCase
{
    use KernHilfe;

    /** @return array<string, array{string, string}> Baustein (mit Symbol, als Link aufrufbar), data-variant */
    public static function symbolButtons(): array
    {
        return [
            'button-back' => ['button-back', 'back'],
            'button-cancel' => ['button-cancel', 'cancel'],
            'button-create' => ['button-create', 'create'],
            'button-delete' => ['button-delete', 'delete'],
            'button-edit' => ['button-edit', 'edit'],
            'button-save' => ['button-save', 'save'],
            'button-show' => ['button-show', 'show'],
        ];
    }

    // AK1
    public function test_layout_bausteine_rendern_im_theme_kern_mit_kit_markup(): void
    {
        $blades = [
            '<x-kit::button>x</x-kit::button>',
            '<x-kit::button-submit>x</x-kit::button-submit>',
            '<x-kit::card-main>x</x-kit::card-main>',
            '<x-kit::card-main-header>x</x-kit::card-main-header>',
            '<x-kit::card-body>x</x-kit::card-body>',
            '<x-kit::card-footer>x</x-kit::card-footer>',
            '<x-kit::show-euro value="1" />',
            '<x-kit::show-date value="2026-10-31" />',
            '<x-kit::breadcrumb>x</x-kit::breadcrumb>',
        ];
        foreach (self::symbolButtons() as [$name]) {
            $blades[] = "<x-kit::{$name}>x</x-kit::{$name}>";
        }

        foreach ($blades as $blade) {
            $html = $this->kernRendern($blade);
            $this->assertStringContainsString('kit-', $html, $blade);
            $this->assertSame([], $this->fremdeKlassen($html), $blade);
        }
    }

    // AK7
    #[DataProvider('symbolButtons')]
    public function test_symbol_button_als_link_hat_kit_button_variante_href_title_und_svg_symbol(string $name, string $variante): void
    {
        $html = $this->kernRendern("<x-kit::{$name} type=\"link\" route=\"/ziel\" tooltip=\"Tip\">Text</x-kit::{$name}>");

        $this->sieHat($html, "//a{$this->kl('kit-button')}[@data-variant='{$variante}'][@href='/ziel'][@title='Tip'][contains(., 'Text')]");
        $this->sieHat($html, "//a/svg{$this->kl('kit-button__icon')}[@aria-hidden='true']");
        $this->sieHatNicht($html, '//*[@data-toggle]');
        $this->sieHatNicht($html, '//i');
        $this->sieHatNicht($html, '//button');
    }

    // AK7
    public function test_button_form_behaelt_type_und_loesch_bindung_und_variante_kommt_aus_prop(): void
    {
        $loeschen = $this->kernRendern('<x-kit::button-delete type="button" value="42" tooltip="Weg">Löschen</x-kit::button-delete>');
        $this->sieHat($loeschen, "//button{$this->kl('kit-button')}[@data-variant='delete'][@type='button'][@data-kit-delete='42'][@title='Weg']");
        $this->sieHatNicht($loeschen, '//*[@data-toggle]');

        $speichern = $this->kernRendern('<x-kit::button-save type="submit">Speichern</x-kit::button-save>');
        $this->sieHat($speichern, "//button{$this->kl('kit-button')}[@data-variant='save'][@type='submit']/svg{$this->kl('kit-button__icon')}");

        $submit = $this->kernRendern('<x-kit::button-submit>Senden</x-kit::button-submit>');
        $this->sieHat($submit, "//button{$this->kl('kit-button')}[@data-variant='submit'][@type='submit']");

        $standard = $this->kernRendern('<x-kit::button>Los</x-kit::button>');
        $this->sieHat($standard, "//button{$this->kl('kit-button')}[@data-variant='primary'][@type='button']");
        $zweit = $this->kernRendern('<x-kit::button variant="secondary">Los</x-kit::button>');
        $this->sieHat($zweit, "//button{$this->kl('kit-button')}[@data-variant='secondary']");
    }

    // AK8
    public function test_card_hat_kopf_rumpf_und_fuss_als_eigene_elemente_und_classcard_haengt_an(): void
    {
        $html = $this->kernRendern('<x-kit::card-main classcard="extra"><x-kit::card-main-header>Kopf</x-kit::card-main-header>'
            .'<x-kit::card-body>Rumpf</x-kit::card-body><x-kit::card-footer>Fuß</x-kit::card-footer></x-kit::card-main>');

        $this->sieHat($html, "//div{$this->kl('kit-card')}{$this->kl('extra')}");
        $this->sieHat($html, "//div{$this->kl('kit-card')}//div{$this->kl('kit-card__header')}[contains(., 'Kopf')]");
        $this->sieHat($html, "//div{$this->kl('kit-card')}//div{$this->kl('kit-card__body')}[contains(., 'Rumpf')]");
        $this->sieHat($html, "//div{$this->kl('kit-card')}//div{$this->kl('kit-card__footer')}[contains(., 'Fuß')]");

        // Gegenprobe: ohne classcard gibt es keine Zusatzklasse
        $schlicht = $this->kernRendern('<x-kit::card-main>x</x-kit::card-main>');
        $this->assertSame([], $this->fremdeKlassen($schlicht));
        $this->sieHat($schlicht, "//div{$this->kl('kit-card')}");
    }

    // AK13
    public function test_show_euro_show_date_und_breadcrumb_im_kit_markup(): void
    {
        $html = $this->kernRendern('<x-kit::show-euro value="1234.5" /><x-kit::show-date value="2026-10-31" />'
            .'<x-kit::breadcrumb><li>Start</li></x-kit::breadcrumb>');

        $this->assertSame(KitAnzeige::euro('1234.5'), $this->text($html, "//*{$this->kl('kit-show-euro')}"));
        config(['laravelkit.theme' => 'bootstrap5']);
        $bootstrap = Blade::render('<x-kit::show-date value="2026-10-31" />');
        $this->assertNotSame('', $this->text($bootstrap, '//div'));
        $this->assertSame($this->text($bootstrap, '//div'), $this->text($html, "//*{$this->kl('kit-show-date')}"));
        $this->sieHat($html, "//nav{$this->kl('kit-breadcrumb')}[@aria-label='breadcrumb']/ol{$this->kl('kit-breadcrumb__list')}");
    }
}
