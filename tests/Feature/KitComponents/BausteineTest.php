<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Bausteine (Kit-Components) in beiden Themes: Vorlage sind die Vue-Komponenten
 * unter vue/tailwind und vue/bootstrap5 (Formularfelder: vue/bootstrap5/forms).
 */
final class BausteineTest extends TestCase
{
    private const THEMES = ['tailwind', 'bootstrap5'];

    private const KOMPONENTEN = [
        'card-main', 'card-main-header', 'card-body', 'card-footer',
        'button-create', 'button-edit', 'button-show', 'button-delete',
        'button-save', 'button-back', 'button-submit', 'button-cancel',
        'input-text', 'input-email', 'input-password', 'input-date', 'input-euro',
        'input-number', 'input-hidden', 'input-file-img', 'text-area', 'checkbox',
        'tabs-header', 'tab-header', 'tabs-body', 'tab-body',
        'breadcrumb', 'show-euro', 'show-date',
    ];

    /** Komponenten mit Slot. */
    private const MIT_SLOT = [
        'card-main', 'card-main-header', 'card-body', 'card-footer',
        'button-create', 'button-edit', 'button-show', 'button-delete',
        'button-save', 'button-back', 'button-submit', 'button-cancel',
        'text-area', 'checkbox', 'tabs-header', 'tab-header', 'tabs-body', 'tab-body', 'breadcrumb',
    ];

    /** Hauptelement je Komponente: [Tag, id übernimmt die Komponente nicht selbst aus name]. */
    private const HAUPT = [
        'card-main' => ['div', true], 'card-main-header' => ['div', true],
        'card-body' => ['div', true], 'card-footer' => ['div', true],
        'button-create' => ['a', true], 'button-edit' => ['a', true], 'button-show' => ['a', true],
        'button-delete' => ['a', true], 'button-save' => ['a', true], 'button-back' => ['a', true],
        'button-submit' => ['button', true], 'button-cancel' => ['a', true],
        'input-text' => ['input', false], 'input-email' => ['input', false],
        'input-password' => ['input', false], 'input-date' => ['input', false],
        'input-euro' => ['input', false], 'input-number' => ['input', false],
        'input-hidden' => ['input', false], 'input-file-img' => ['input', false],
        'text-area' => ['textarea', false], 'checkbox' => ['input', false],
        'tabs-header' => ['ul', true], 'tab-header' => ['button', false],
        'tabs-body' => ['div', true], 'tab-body' => ['div', false],
        'breadcrumb' => ['nav', true], 'show-euro' => ['div', true], 'show-date' => ['div', true],
    ];

    // ---------------------------------------------------------------- Helfer

    private function rendern(string $theme, string $blade, array $daten = []): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade, $daten);
    }

    private static function k(string ...$klassen): string
    {
        return implode('', array_map(
            fn (string $c) => "[contains(concat(' ', normalize-space(@class), ' '), ' {$c} ')]",
            $klassen,
        ));
    }

    private static function nicht(string $klasse): string
    {
        return "[not(contains(concat(' ', normalize-space(@class), ' '), ' {$klasse} '))]";
    }

    private function xpath(string $html): DOMXPath
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function trifft(string $html, string $xpath): bool
    {
        $ergebnis = $this->xpath($html)->evaluate($xpath);

        return $ergebnis instanceof DOMNodeList ? $ergebnis->length > 0 : (bool) $ergebnis;
    }

    /** @param  list<string>  $xpaths */
    private function pruefeAlle(string $html, array $xpaths): void
    {
        foreach ($xpaths as $xpath) {
            $this->assertTrue($this->trifft($html, $xpath), "XPath trifft nicht: {$xpath}\nHTML: {$html}");
        }
    }

    /** @return list<string> */
    private function vueSpuren(string $html): array
    {
        $spuren = [];
        if (preg_match_all('/<[a-z][a-z0-9]*-[a-z0-9-]*[\s>\/]/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }
        foreach (self::KOMPONENTEN as $name) {
            if (preg_match('/<'.preg_quote($name, '/').'[\s>\/]/i', $html)) {
                $spuren[] = "<{$name}";
            }
        }
        if (preg_match_all('/\sv-[\w:.\-]*(?=[=\s>\/])/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }
        if (preg_match_all('/\s:[\w.\-]+(?=[=\s>\/])/i', $html, $m)) {
            $spuren = array_merge($spuren, $m[0]);
        }

        return $spuren;
    }

    // ----------------------------------------------------------- Provider

    /** @return array<string, array{string, string}> */
    public static function alleKomponenten(): array
    {
        $daten = [];
        foreach (self::THEMES as $theme) {
            foreach (self::KOMPONENTEN as $name) {
                $daten["{$theme} {$name}"] = [$theme, $name];
            }
        }

        return $daten;
    }

    /** @return array<string, array{string, string}> */
    public static function komponentenMitSlot(): array
    {
        return array_filter(
            self::alleKomponenten(),
            fn (array $fall) => in_array($fall[1], self::MIT_SLOT, true),
        );
    }

    /** Aufruf, der für jede Komponente gültig ist. */
    private function aufruf(string $name, string $attribute = '', string $slot = 'x'): string
    {
        $standard = match (true) {
            str_starts_with($name, 'button-') && $name !== 'button-submit' => 'type="link" route="/r"',
            in_array($name, ['tab-header', 'tab-body'], true) => 'name="t"',
            str_starts_with($name, 'input-') || in_array($name, ['text-area', 'checkbox'], true) => 'name="n"',
            default => '',
        };

        return "<x-kit::{$name} {$standard} {$attribute}>{$slot}</x-kit::{$name}>";
    }

    /** @return array<string, array{string, string, list<string>}> */
    public static function grundgeruest(): array
    {
        $f = [];
        $add = function (string $theme, string $titel, string $blade, array $xpaths) use (&$f): void {
            $f["{$theme}: {$titel}"] = [$theme, $blade, $xpaths];
        };

        // --- Cards
        $add('tailwind', 'card-main', '<x-kit::card-main>Inhalt</x-kit::card-main>',
            ['//div'.self::k('card-main')."[contains(., 'Inhalt')]"]);
        $add('tailwind', 'card-main-header', '<x-kit::card-main-header>Inhalt</x-kit::card-main-header>',
            ['//div'.self::k('card-main-header')."[contains(., 'Inhalt')]"]);
        $add('tailwind', 'card-body', '<x-kit::card-body>Inhalt</x-kit::card-body>',
            ['//div'.self::k('card-body')."[contains(., 'Inhalt')]"]);
        $add('tailwind', 'card-footer', '<x-kit::card-footer>Inhalt</x-kit::card-footer>',
            ['//div'.self::k('card-footer')."[contains(., 'Inhalt')]"]);
        $add('bootstrap5', 'card-main', '<x-kit::card-main>Inhalt</x-kit::card-main>',
            ['//div'.self::k('card', 'card-main')."[contains(., 'Inhalt')]"]);
        $add('bootstrap5', 'card-main mit classcard', '<x-kit::card-main classcard="bg-dark">Inhalt</x-kit::card-main>',
            ['//div'.self::k('card', 'card-main', 'bg-dark').self::nicht('bg-light')]);
        $add('bootstrap5', 'card-main-header', '<x-kit::card-main-header>Inhalt</x-kit::card-main-header>',
            ['//div'.self::k('card-main-header')."[contains(., 'Inhalt')]"]);
        $add('bootstrap5', 'card-main-header mit classheader', '<x-kit::card-main-header classheader="fw-bold">Inhalt</x-kit::card-main-header>',
            ['//div'.self::k('card-main-header', 'fw-bold').self::nicht('font-weight-bold')]);
        $add('bootstrap5', 'card-body', '<x-kit::card-body>Inhalt</x-kit::card-body>',
            ['//div'.self::k('card-body')."[contains(., 'Inhalt')]"]);
        $add('bootstrap5', 'card-footer', '<x-kit::card-footer>Inhalt</x-kit::card-footer>',
            ['//div'.self::k('card-footer')."[contains(., 'Inhalt')]"]);

        // --- Buttons: als Link und als Button, jeweils mit route, tooltip und Text
        foreach (self::THEMES as $theme) {
            foreach (['create', 'edit', 'show', 'delete', 'save', 'back', 'cancel'] as $b) {
                $klassen = self::k('btn', "btn-{$b}");
                $add($theme, "button-{$b} als Link",
                    "<x-kit::button-{$b} type=\"link\" route=\"/ziel\" tooltip=\"Tip\">Text</x-kit::button-{$b}>",
                    ["//a{$klassen}[@href='/ziel'][@title='Tip'][contains(., 'Text')]", '(count(//button) = 0)']);
                $typ = ($theme === 'bootstrap5' && $b === 'delete') ? '' : "[@type='button']";
                $add($theme, "button-{$b} als Button",
                    "<x-kit::button-{$b} type=\"button\" tooltip=\"Tip\">Text</x-kit::button-{$b}>",
                    ["//button{$klassen}{$typ}[@title='Tip'][contains(., 'Text')]", '(count(//a) = 0)']);
            }
            $add($theme, 'button-submit', '<x-kit::button-submit tooltip="Tip">Text</x-kit::button-submit>',
                ['//button'.self::k('btn', 'btn-submit')."[@type='submit'][@title='Tip'][contains(., 'Text')]"]);
        }

        // --- Formularfelder Tailwind
        $add('tailwind', 'input-text mit Label', '<x-kit::input-text name="vorname" label="Vorname" value="Max" />', [
            "//div[contains(@class,'w-full')]//label".self::k('hlabel')."[@for='vorname'][normalize-space(.)='Vorname']",
            "//input[@type='text'][@id='vorname'][@name='vorname'][@value='Max']".self::k('input-text'),
        ]);
        $add('tailwind', 'input-text mit id', '<x-kit::input-text name="vorname" id="v1" label="Vorname" />', [
            "//label[@for='v1']", "//input[@id='v1'][@name='vorname']",
        ]);
        $add('tailwind', 'input-text ohne Label', '<x-kit::input-text name="n" />', [
            "//input[@type='text'][@id='n'][@name='n']", 'not(//label)',
        ]);
        $add('tailwind', 'input-email', '<x-kit::input-email name="mail" value="a@b.de" />',
            ["//input[@type='email'][@id='mail'][@name='mail'][@value='a@b.de']".self::k('input-email')]);
        $add('tailwind', 'input-password', '<x-kit::input-password name="pw" label="Passwort" />', [
            '//label'.self::k('hlabel')."[@for='pw']",
            "//input[@type='password'][@id='pw'][@name='pw']".self::k('input-password'),
        ]);
        $add('tailwind', 'input-date', '<x-kit::input-date name="datum" value="2026-10-31" />',
            ["//input[@type='date'][@id='datum'][@name='datum'][@value='2026-10-31']".self::k('input-date')]);
        $add('tailwind', 'input-euro', '<x-kit::input-euro name="preis" value="9.90" step="0.05" />', [
            "//input[@type='number'][@id='preis'][@name='preis'][@value='9.90'][@step='0.05'][@lang='de-DE'][@aria-label='Euro']".self::k('input-euro'),
            "//span[contains(., '€')]",
        ]);
        $add('tailwind', 'input-number', '<x-kit::input-number name="menge" value="3" step="1" />',
            ["//input[@type='number'][@id='menge'][@name='menge'][@value='3'][@step='1']".self::k('input-number')]);
        $add('tailwind', 'input-hidden', '<x-kit::input-hidden name="feld" value="7" />',
            ["//input[@type='hidden'][@name='feld'][@value='7']"]);
        $add('tailwind', 'input-file-img', '<x-kit::input-file-img name="bild" />',
            ["//input[@type='file'][@id='bild'][@name='bild'][@accept='image/png, image/jpeg']".self::k('input-file-img')]);
        $add('tailwind', 'text-area', '<x-kit::text-area name="bio" rows="5" value="Hallo" />',
            ["//textarea[@id='bio'][@name='bio'][@rows='5']".self::k('text-area')."[normalize-space(.)='Hallo']"]);
        $add('tailwind', 'text-area mit Slot', '<x-kit::text-area name="bio">Aus Slot</x-kit::text-area>',
            ["//textarea[@name='bio']"."[normalize-space(.)='Aus Slot']"]);
        $add('tailwind', 'checkbox angehakt', '<x-kit::checkbox name="agb" value="1">Ich stimme zu</x-kit::checkbox>', [
            "//input[@type='checkbox'][@id='agb'][@name='agb'][@value='1'][@checked]".self::k('checkbox'),
            '//label'.self::k('hlabel')."[@for='agb'][contains(., 'Ich stimme zu')]",
        ]);
        $add('tailwind', 'checkbox nicht angehakt', '<x-kit::checkbox name="agb" value="0">Ich stimme zu</x-kit::checkbox>',
            ["//input[@type='checkbox'][@name='agb'][@value='0'][not(@checked)]"]);

        // --- Formularfelder Bootstrap 5
        $add('bootstrap5', 'input-text', '<x-kit::input-text name="vorname" value="Max" />',
            ["//input[@type='text'][@id='vorname'][@name='vorname'][@value='Max']".self::k('form-control', 'input-text')]);
        $add('bootstrap5', 'input-email', '<x-kit::input-email name="mail" value="a@b.de" />',
            ["//input[@type='email'][@id='mail'][@name='mail'][@value='a@b.de']".self::k('form-control', 'input-email')]);
        $add('bootstrap5', 'input-password', '<x-kit::input-password name="pw" />',
            ['//div'.self::k('input-group')."//input[@type='password'][@id='pw'][@name='pw']".self::k('form-control', 'input-password')]);
        $add('bootstrap5', 'input-date', '<x-kit::input-date name="datum" value="2026-10-31" />',
            ["//input[@type='date'][@id='datum'][@name='datum'][@value='2026-10-31']".self::k('form-control', 'input-date')]);
        $add('bootstrap5', 'input-euro', '<x-kit::input-euro name="preis" value="9.90" step="0.05" />', [
            '//div'.self::k('input-group')."//input[@type='number'][@id='preis'][@name='preis'][@value='9.90'][@step='0.05'][@lang='de-DE'][@aria-label='Euro']".self::k('form-control', 'input-euro'),
            '//span'.self::k('input-group-text')."[contains(., '€')]",
        ]);
        $add('bootstrap5', 'input-number', '<x-kit::input-number name="menge" value="3" step="1" />',
            ["//input[@type='number'][@id='menge'][@name='menge'][@value='3'][@step='1']".self::k('form-control', 'input-number')]);
        $add('bootstrap5', 'input-hidden', '<x-kit::input-hidden name="feld" value="7" />',
            ["//input[@type='hidden'][@name='feld'][@value='7']"]);
        $add('bootstrap5', 'input-file-img', '<x-kit::input-file-img name="bild" />',
            ["//input[@type='file'][@id='bild'][@name='bild'][@accept='image/png, image/jpeg']".self::k('form-control')]);
        $add('bootstrap5', 'text-area', '<x-kit::text-area name="bio" rows="5" value="Hallo" />',
            ["//textarea[@id='bio'][@name='bio'][@rows='5']".self::k('form-control', 'text-area')."[normalize-space(.)='Hallo']"]);
        $add('bootstrap5', 'text-area mit Slot', '<x-kit::text-area name="bio">Aus Slot</x-kit::text-area>',
            ["//textarea[@name='bio']"."[normalize-space(.)='Aus Slot']"]);
        $add('bootstrap5', 'checkbox angehakt', '<x-kit::checkbox name="agb" value="1" :checked="true">Ich stimme zu</x-kit::checkbox>', [
            '//div'.self::k('form-check')."//input[@type='checkbox'][@id='agb'][@name='agb'][@value='1'][@checked]".self::k('form-check-input'),
            '//label'.self::k('form-check-label')."[@for='agb'][contains(., 'Ich stimme zu')]",
        ]);
        $add('bootstrap5', 'checkbox nicht angehakt', '<x-kit::checkbox name="agb" value="1" :checked="false">Ich stimme zu</x-kit::checkbox>',
            ["//input[@type='checkbox'][@name='agb'][@value='1'][not(@checked)]"]);

        // --- Tabs, Breadcrumb, Anzeige
        $add('tailwind', 'tabs-header', '<x-kit::tabs-header>Reiter</x-kit::tabs-header>',
            ['//ul'.self::k('tabs-header')."[@role='tablist'][contains(., 'Reiter')]"]);
        $add('tailwind', 'tab-header', '<x-kit::tab-header name="a">Reiter A</x-kit::tab-header>',
            ["//li//button[@type='button'][@id='a-tab'][@role='tab']".self::k('tab-header')."[contains(., 'Reiter A')]"]);
        $add('tailwind', 'tabs-body', '<x-kit::tabs-body>Inhalte</x-kit::tabs-body>',
            ['//div'.self::k('tabs-body')."[contains(., 'Inhalte')]"]);
        $add('tailwind', 'tab-body', '<x-kit::tab-body name="a">Inhalt A</x-kit::tab-body>',
            ["//div[@id='a'][@role='tabpanel'][@aria-labelledby='a-tab']".self::k('tab-body')."[contains(., 'Inhalt A')]"]);
        $add('bootstrap5', 'tabs-header', '<x-kit::tabs-header>Reiter</x-kit::tabs-header>',
            ['//ul'.self::k('nav', 'nav-tabs', 'tabs-header')."[@role='tablist'][contains(., 'Reiter')]"]);
        $add('bootstrap5', 'tab-header', '<x-kit::tab-header name="a">Reiter A</x-kit::tab-header>',
            ['//li'.self::k('nav-item')."//button[@type='button'][@id='a-tab'][@role='tab'][@aria-controls='a']".self::k('tab-header', 'nav-link').self::nicht('active')."[contains(., 'Reiter A')]"]);
        $add('bootstrap5', 'tab-header aktiv', '<x-kit::tab-header name="a" :active="true">Reiter A</x-kit::tab-header>',
            ["//button[@id='a-tab']".self::k('tab-header', 'nav-link', 'active')."[@aria-selected='true']"]);
        $add('bootstrap5', 'tabs-body', '<x-kit::tabs-body>Inhalte</x-kit::tabs-body>',
            ['//div'.self::k('tab-content', 'tabs-body')."[contains(., 'Inhalte')]"]);
        $add('bootstrap5', 'tab-body', '<x-kit::tab-body name="a">Inhalt A</x-kit::tab-body>',
            ["//div[@id='a'][@role='tabpanel'][@aria-labelledby='a-tab']".self::k('tab-body', 'tab-pane', 'fade').self::nicht('active')."[contains(., 'Inhalt A')]"]);
        $add('bootstrap5', 'tab-body aktiv', '<x-kit::tab-body name="a" :active="true">Inhalt A</x-kit::tab-body>',
            ["//div[@id='a']".self::k('tab-body', 'tab-pane', 'fade', 'show', 'active')]);

        foreach (self::THEMES as $theme) {
            $add($theme, 'breadcrumb', '<x-kit::breadcrumb><li class="breadcrumb-item">Start</li></x-kit::breadcrumb>',
                ["//nav[@aria-label='breadcrumb']/ol".self::k('breadcrumb')."/li[contains(., 'Start')]"]);
            $add($theme, 'show-euro', '<x-kit::show-euro value="1234.5" />',
                ['//div'.self::k('show-euro')."[@value='1234.5'][normalize-space(.)='1.234,50 €']"]);
            $add($theme, 'show-date', '<x-kit::show-date value="2026-10-31" />',
                ['//div'.self::k('show-date')."[@value='2026-10-31'][normalize-space(.)='31.10.2026']"]);
        }

        return $f;
    }

    /** @return array<string, array{string, string, list<string>}> */
    public static function standardwerte(): array
    {
        $f = [];
        $add = function (string $theme, string $titel, string $blade, array $xpaths) use (&$f): void {
            $f["{$theme}: {$titel}"] = [$theme, $blade, $xpaths];
        };

        $tip = fn (string $t) => $t === '' ? '[not(@title) or @title=""]' : "[@title='{$t}']";

        // [Button, Tag, type-Default, Tooltip tailwind, Tooltip bootstrap5]
        $buttons = [
            ['create', 'a', null, 'Neu erstellen', 'Neuen Eintrag erstellen'],
            ['edit', 'a', null, 'Bearbeiten', 'Bearbeiten'],
            ['show', 'a', null, 'Anzeigen', 'Anzeigen'],
            ['back', 'a', null, 'Zurück', ''],
        ];
        foreach (self::THEMES as $theme) {
            $t = fn (array $b) => $theme === 'tailwind' ? $b[3] : $b[4];
            foreach ($buttons as $b) {
                $add($theme, "button-{$b[0]} ohne Attribute", "<x-kit::button-{$b[0]} />",
                    ['//a'.self::k('btn', "btn-{$b[0]}")."[@href='#']".$tip($t($b)), '(count(//button) = 0)']);
            }
            $add($theme, 'button-delete ohne Attribute', '<x-kit::button-delete />', [
                '//button'.self::k('btn', 'btn-delete').($theme === 'tailwind' ? "[@type='button']" : '').$tip('Löschen'),
                '(count(//a) = 0)',
            ]);
            $add($theme, 'button-save ohne Text', '<x-kit::button-save />', [
                '//button'.self::k('btn', 'btn-save')."[@type='submit']".$tip($theme === 'tailwind' ? 'Speichern' : ''),
                '(count(//a) = 0)',
            ]);
            $add($theme, 'button-submit ohne Attribute', '<x-kit::button-submit />',
                ['//button'.self::k('btn', 'btn-submit')."[@type='submit']".$tip('')]);
        }
        $add('tailwind', 'button-cancel ohne Attribute', '<x-kit::button-cancel />', [
            '//button'.self::k('btn', 'btn-cancel')."[@type='button']".$tip('Abbrechen'), '(count(//a) = 0)',
        ]);
        $add('bootstrap5', 'button-cancel ohne Attribute', '<x-kit::button-cancel />', [
            '//a'.self::k('btn', 'btn-cancel')."[@href='#']".$tip('Abbruch'), '(count(//button) = 0)',
        ]);

        $add('bootstrap5', 'card-main ohne Attribute', '<x-kit::card-main>x</x-kit::card-main>',
            ['//div'.self::k('card', 'card-main', 'bg-light', 'mb-3')]);
        $add('bootstrap5', 'card-main-header ohne Attribute', '<x-kit::card-main-header>x</x-kit::card-main-header>',
            ['//div'.self::k('card-main-header', 'font-weight-bold')]);
        $add('bootstrap5', 'input-euro ohne step', '<x-kit::input-euro name="p" />', ["//input[@name='p'][@step='0.01']"]);
        $add('bootstrap5', 'input-number ohne step', '<x-kit::input-number name="p" />', ["//input[@name='p'][@step='0.01']"]);
        $add('bootstrap5', 'text-area ohne rows', '<x-kit::text-area name="p" />', ["//textarea[@name='p'][@rows='3']"]);
        $add('bootstrap5', 'checkbox ohne value', '<x-kit::checkbox name="p">x</x-kit::checkbox>',
            ["//input[@type='checkbox'][@name='p'][@value='1'][not(@checked)]"]);
        $add('tailwind', 'checkbox ohne value ist nicht angehakt', '<x-kit::checkbox name="p">x</x-kit::checkbox>',
            ["//input[@type='checkbox'][@name='p'][not(@checked)]"]);

        return $f;
    }

    // ----------------------------------------------------------------- AK2

    // AK2
    #[DataProvider('alleKomponenten')]
    public function test_kit_component_existiert_im_theme(string $theme, string $name): void
    {
        $html = $this->rendern($theme, $this->aufruf($name));

        $this->assertNotSame('', trim($html), "<x-kit::{$name}> erzeugt im Theme {$theme} nichts");
        $this->assertStringContainsString('<', $html);
    }

    // ----------------------------------------------------------------- AK3

    // AK3
    #[DataProvider('grundgeruest')]
    public function test_html_grundgeruest_und_klassen_wie_die_vue_komponente(string $theme, string $blade, array $xpaths): void
    {
        $this->pruefeAlle($this->rendern($theme, $blade), $xpaths);
    }

    // AK3
    #[DataProvider('alleKomponenten')]
    public function test_weitere_attribute_landen_am_hauptelement(string $theme, string $name): void
    {
        [$tag, $idErlaubt] = self::HAUPT[$name];
        $id = $idErlaubt ? 'id="meinid"' : '';

        $html = $this->rendern($theme, $this->aufruf($name, "class=\"extra\" required data-x=\"wert\" {$id}"));

        $this->pruefeAlle($html, [
            "//{$tag}".self::k('extra')."[@data-x='wert'][@required]",
        ]);
        if ($idErlaubt) {
            $this->pruefeAlle($html, ["//{$tag}[@id='meinid']"]);
        }
    }

    // AK3
    #[DataProvider('themesListe')]
    public function test_formular_submit_sendet_dieselben_felder_wie_die_vue_version(string $theme): void
    {
        $html = $this->rendern($theme, <<<'BLADE'
            <form method="post" action="/x">
                <x-kit::input-text name="vorname" value="Max" />
                <x-kit::input-email name="mail" value="a@b.de" />
                <x-kit::input-date name="datum" value="2026-10-31" />
                <x-kit::input-euro name="preis" value="9.90" />
                <x-kit::input-number name="menge" value="3" />
                <x-kit::input-hidden name="kennung" value="7" />
                <x-kit::text-area name="bio" value="Zeile" />
                <x-kit::checkbox name="agb" value="1" :checked="true">AGB</x-kit::checkbox>
                <x-kit::checkbox name="newsletter" value="0">Newsletter</x-kit::checkbox>
                <x-kit::button-submit>Senden</x-kit::button-submit>
            </form>
            BLADE);

        $xp = $this->xpath($html);
        $gesendet = [];
        foreach ($xp->query('//form//input[@name] | //form//textarea[@name]') as $feld) {
            if ($feld->hasAttribute('disabled')) {
                continue;
            }
            $typ = strtolower($feld->getAttribute('type'));
            if (in_array($typ, ['checkbox', 'radio'], true) && ! $feld->hasAttribute('checked')) {
                continue;
            }
            $wert = $feld->nodeName === 'textarea' ? trim($feld->textContent) : $feld->getAttribute('value');
            $gesendet[$feld->getAttribute('name')] = $wert;
        }
        ksort($gesendet);

        $erwartet = [
            'agb' => '1', 'bio' => 'Zeile', 'datum' => '2026-10-31', 'kennung' => '7',
            'mail' => 'a@b.de', 'menge' => '3', 'preis' => '9.90', 'vorname' => 'Max',
        ];

        $this->assertSame($erwartet, $gesendet);
    }

    /** @return array<string, array{string}> */
    public static function themesListe(): array
    {
        return ['tailwind' => ['tailwind'], 'bootstrap5' => ['bootstrap5']];
    }

    // ----------------------------------------------------------------- AK4

    // AK4
    #[DataProvider('alleKomponenten')]
    public function test_keine_vue_tags_und_keine_v_oder_doppelpunkt_attribute(string $theme, string $name): void
    {
        $html = $this->rendern($theme, $this->aufruf($name, ':data-x="$wert" data-y="a"', '{{ $wert }}'), ['wert' => 'x']);

        $this->assertNotSame('', trim($html));
        $this->assertSame([], $this->vueSpuren($html), "Vue-Spuren in {$name}/{$theme}: {$html}");
    }

    // AK4
    public function test_die_suche_nach_vue_spuren_erkennt_tags_und_attribute(): void
    {
        $this->assertNotSame([], $this->vueSpuren('<card-main>x</card-main>'));
        $this->assertNotSame([], $this->vueSpuren('<a v-if="x" href="#">x</a>'));
        $this->assertNotSame([], $this->vueSpuren('<a :href="x">x</a>'));
        $this->assertSame([], $this->vueSpuren('<a href="#" data-x="a:b" class="btn">x</a>'));
    }

    // AK4
    #[DataProvider('alleKomponenten')]
    public function test_ausdruck_im_attribut_erscheint_als_text_und_wird_nicht_berechnet(string $theme, string $name): void
    {
        $html = $this->rendern($theme, $this->aufruf($name, ':data-x="$wert"'), ['wert' => '{{ 7*7 }}']);

        $this->assertTrue($this->trifft($html, "//*[@data-x='{{ 7*7 }}']"), $html);
        $this->assertFalse($this->trifft($html, "//*[@data-x='49']"), $html);
    }

    // AK4
    #[DataProvider('komponentenMitSlot')]
    public function test_ausdruck_im_slot_erscheint_als_text_und_wird_nicht_berechnet(string $theme, string $name): void
    {
        $html = $this->rendern($theme, $this->aufruf($name, '', '{{ $wert }}'), ['wert' => '{{ 7*7 }}']);

        $this->assertTrue($this->trifft($html, "//*[contains(., '{{ 7*7 }}')]"), $html);
        $this->assertFalse($this->trifft($html, "//*[normalize-space(.)='49']"), $html);
    }

    // AK4
    #[DataProvider('themesListe')]
    public function test_ausdruck_als_value_und_label_erscheint_als_text(string $theme): void
    {
        $wert = '{{ 7*7 }}';
        $html = $this->rendern($theme, <<<'BLADE'
            <x-kit::input-text name="a" :value="$wert" :label="$wert" />
            <x-kit::input-email name="b" :value="$wert" />
            <x-kit::input-hidden name="c" :value="$wert" />
            <x-kit::text-area name="d" :value="$wert" />
            BLADE, ['wert' => $wert]);

        $this->pruefeAlle($html, [
            "//input[@name='a'][@value='{{ 7*7 }}']",
            "//input[@name='b'][@value='{{ 7*7 }}']",
            "//input[@name='c'][@value='{{ 7*7 }}']",
            "//textarea[@name='d'][contains(., '{{ 7*7 }}')]",
        ]);
        $this->assertStringNotContainsString('49', $html);
    }

    // AK4
    #[DataProvider('alleKomponenten')]
    public function test_html_in_attribut_und_slot_wird_escaped(string $theme, string $name): void
    {
        $angriff = '"><script>alert(1)</script><b>';
        $html = $this->rendern($theme, $this->aufruf($name, ':data-x="$wert"', '{{ $wert }}'), ['wert' => $angriff]);

        $this->assertNotSame('', trim($html));
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertTrue($this->trifft($html, '//*[@data-x]'), 'Attribut fehlt, Aufbau greift nicht: '.$html);
    }

    // ------------------------------------------------------------ Randfall 1

    // Randfall 1
    #[DataProvider('standardwerte')]
    public function test_ohne_pflicht_attribut_gilt_der_default_der_vue_version(string $theme, string $blade, array $xpaths): void
    {
        $this->pruefeAlle($this->rendern($theme, $blade), $xpaths);
    }

    // Randfall 1
    #[DataProvider('themesListe')]
    public function test_show_komponenten_ohne_value_haben_value_default_leer(string $theme): void
    {
        $html = $this->rendern($theme, '<x-kit::show-euro /><x-kit::show-date />');

        $this->pruefeAlle($html, [
            '//div'.self::k('show-euro').'[@value=""]',
            '//div'.self::k('show-date').'[@value=""]',
        ]);
    }

    // ------------------------------------------------------------ Randfall 2

    private function routeMitOldInput(string $theme, string $blade): void
    {
        config(['laravelkit.theme' => $theme]);
        Route::middleware('web')->get('/_kit/old', fn () => Blade::render($blade));
    }

    // Randfall 2
    #[DataProvider('themesListe')]
    public function test_old_wert_aus_der_view_steht_vor_value(string $theme): void
    {
        $this->routeMitOldInput($theme, <<<'BLADE'
            <x-kit::input-text name="vorname" :value="old('vorname', 'Db')" />
            <x-kit::input-email name="mail" :value="old('mail', 'db@x.de')" />
            <x-kit::text-area name="bio" :value="old('bio', 'Db-Text')" />
            BLADE);

        $mitOld = $this->withSession(['_old_input' => ['vorname' => 'Alt', 'mail' => 'alt@x.de', 'bio' => 'Alt-Text']])
            ->get('/_kit/old')->assertOk()->getContent();

        $this->pruefeAlle($mitOld, [
            "//input[@name='vorname'][@value='Alt']",
            "//input[@name='mail'][@value='alt@x.de']",
            "//textarea[@name='bio'][normalize-space(.)='Alt-Text']",
        ]);
    }

    // Randfall 2
    #[DataProvider('themesListe')]
    public function test_ohne_old_wert_zeigt_das_feld_den_uebergebenen_value(string $theme): void
    {
        $this->routeMitOldInput($theme, <<<'BLADE'
            <x-kit::input-text name="vorname" :value="old('vorname', 'Db')" />
            <x-kit::text-area name="bio" :value="old('bio', 'Db-Text')" />
            BLADE);

        $html = $this->get('/_kit/old')->assertOk()->getContent();

        $this->pruefeAlle($html, [
            "//input[@name='vorname'][@value='Db']",
            "//textarea[@name='bio'][normalize-space(.)='Db-Text']",
        ]);
    }

    // Randfall 2
    #[DataProvider('themesListe')]
    public function test_component_uebernimmt_value_unveraendert_und_ruft_kein_eigenes_old_auf(string $theme): void
    {
        $this->routeMitOldInput($theme, <<<'BLADE'
            <x-kit::input-text name="vorname" value="Explizit" />
            <x-kit::input-text name="nachname" :value="old('nachname', 'Db')" />
            <x-kit::text-area name="bio" value="Explizit-Text" />
            BLADE);

        $html = $this->withSession(['_old_input' => ['vorname' => 'Alt', 'nachname' => 'Alt-Nach', 'bio' => 'Alt-Text']])
            ->get('/_kit/old')->assertOk()->getContent();

        // Gegenprobe: old() der View greift, also ist der Aufbau wirksam
        $this->pruefeAlle($html, [
            "//input[@name='nachname'][@value='Alt-Nach']",
            "//input[@name='vorname'][@value='Explizit']",
            "//textarea[@name='bio'][normalize-space(.)='Explizit-Text']",
        ]);
    }
}
