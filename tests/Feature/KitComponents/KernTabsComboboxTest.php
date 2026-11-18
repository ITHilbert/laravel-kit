<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Theme `kern` der Blade-Bausteine Tabs und Combobox (Spec blade-kern, T3): Markup, das js/tabs.js und
 * js/combobox.js binden (Verhalten der Skripte: tests/js/kernTabs.test.mjs, kernCombobox.test.mjs).
 */
final class KernTabsComboboxTest extends TestCase
{
    use KernHilfe;

    private function tabs(): string
    {
        return $this->kernRendern('<x-kit::tabs-header><x-kit::tab-header name="a" active>A</x-kit::tab-header>'
            .'<x-kit::tab-header name="b">B</x-kit::tab-header></x-kit::tabs-header>'
            .'<x-kit::tabs-body><x-kit::tab-body name="a" active>PA</x-kit::tab-body>'
            .'<x-kit::tab-body name="b">PB</x-kit::tab-body></x-kit::tabs-body>');
    }

    // AK1
    public function test_tabs_und_combobox_rendern_im_theme_kern_nur_mit_kit_klassen(): void
    {
        $html = $this->tabs().$this->kernRendern('<x-kit::combobox name="land" />');

        $this->assertStringContainsString('kit-tabs__tab', $html);
        $this->assertStringContainsString('kit-combobox', $html);
        $this->assertSame([], $this->fremdeKlassen($html), $html);
    }

    // AK9
    public function test_tabs_haben_rollen_aria_und_aktiv_zustand(): void
    {
        $html = $this->tabs();

        $this->sieHat($html, "//*{$this->kl('kit-tabs__list')}[@role='tablist'][@data-kit-tabs]");
        // Bindung für js/tabs.js (Namen siehe tests/js/kernTabs.test.mjs)
        $this->sieHat($html, "//button{$this->kl('kit-tabs__tab')}[@data-kit-tab='a']");
        $this->sieHat($html, "//*{$this->kl('kit-tabs__body')}[@data-kit-tabs-body]");
        $this->sieHat($html, "//*{$this->kl('kit-tabs__panel')}[@data-kit-tab-body='b']");
        $this->assertSame('2', (string) $this->dom($html)->evaluate("count(//button{$this->kl('kit-tabs__tab')}[@role='tab'][@aria-controls])"));
        $this->sieHat($html, "//*{$this->kl('kit-tabs__body')}");
        $this->assertSame('2', (string) $this->dom($html)->evaluate("count(//*{$this->kl('kit-tabs__panel')}[@role='tabpanel'])"));

        $this->sieHat($html, "//button{$this->kl('kit-tabs__tab')}{$this->kl('is-active')}[@aria-selected='true'][@aria-controls='a']");
        $this->sieHat($html, "//button{$this->kl('kit-tabs__tab')}[@aria-selected='false'][@aria-controls='b']");
        $this->sieHatNicht($html, "//button{$this->kl('kit-tabs__tab')}[@aria-controls='b']{$this->kl('is-active')}");
        $this->sieHat($html, "//*{$this->kl('kit-tabs__panel')}{$this->kl('is-active')}[@id='a'][not(@hidden)]");
        $this->sieHat($html, "//*{$this->kl('kit-tabs__panel')}[@id='b'][@hidden][not(contains(@class, 'is-active'))]");
        // Gegenprobe zu „hidden“: das aktive Panel trägt es nicht
        $this->sieHatNicht($html, "//*{$this->kl('kit-tabs__panel')}{$this->kl('is-active')}[@hidden]");
    }

    // AK11
    public function test_combobox_rendert_verstecktes_select_mit_gewaehlter_option(): void
    {
        $html = $this->kernRendern('<x-kit::combobox name="land" label="Land" :options="[\'de\' => \'Deutschland\', \'at\' => \'Österreich\']" value="at" />');

        $this->sieHat($html, "//div{$this->kl('kit-combobox')}/label{$this->kl('kit-combobox__label')}");
        $select = "//div{$this->kl('kit-combobox')}//select{$this->kl('kit-combobox__select')}[@name='land'][@data-kit-combobox]";
        $this->sieHat($html, $select);
        $this->sieHat($html, $select."/option[@value='at'][@selected]");
        $this->sieHatNicht($html, $select."/option[@value='de'][@selected]");
    }

    // Randfall 2
    public function test_combobox_ohne_options_ist_ein_leeres_select(): void
    {
        $html = $this->kernRendern('<x-kit::combobox name="land" />');

        $this->sieHat($html, "//select{$this->kl('kit-combobox__select')}[@name='land']");
        $this->sieHatNicht($html, '//option');
    }

    // Randfall 3
    public function test_tab_body_ohne_active_ist_hidden_ohne_is_active(): void
    {
        $html = $this->kernRendern('<x-kit::tabs-body><x-kit::tab-body name="x">X</x-kit::tab-body></x-kit::tabs-body>');

        $this->sieHat($html, "//*{$this->kl('kit-tabs__panel')}[@hidden]");
        $this->sieHatNicht($html, '//*[contains(@class, "is-active")]');
    }
}
