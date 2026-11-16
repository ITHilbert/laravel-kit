<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Combobox ohne jQuery UI (#1359): Installer, CHANGELOG und README. Quelltext-Prüfungen (Arch).
 */
#[Group('arch')]
final class ComboboxOhneJqueryUiTest extends TestCase
{
    private function lies(string $relativ): string
    {
        $pfad = dirname(__DIR__, 2).'/'.$relativ;
        $this->assertFileExists($pfad);
        $text = (string) file_get_contents($pfad);
        $this->assertNotSame('', $text);

        return $text;
    }

    // AK9
    public function test_installer_installiert_jquery_aber_nicht_jquery_ui(): void
    {
        $text = $this->lies('src/Commands/LaravelKitInstallMix.php');

        $this->assertMatchesRegularExpression('/npm install jquery(?![-\w])/', $text, 'Gegenprobe: jquery bleibt (DataTables)');
        $this->assertStringNotContainsString('npm install jquery-ui', $text);
    }

    // AK10
    public function test_changelog_hat_abschnitt_1_7_0_mit_hinweis_auf_combobox_ohne_jquery_ui(): void
    {
        $text = $this->lies('CHANGELOG.md');

        $this->assertMatchesRegularExpression('/^## 1\.6\.1\b/m', $text, 'Gegenprobe: bestehende Abschnitte werden gefunden');
        $this->assertSame(1, preg_match('/^## 1\.7\.0\b.*?(?=^## |\z)/ms', $text, $treffer), 'Abschnitt ## 1.7.0 fehlt');
        $this->assertMatchesRegularExpression('/combobox/i', $treffer[0]);
        $this->assertMatchesRegularExpression('/jquery[ -]?ui/i', $treffer[0]);
    }

    // AK10
    public function test_readme_nennt_initcombobox_und_dass_jquery_ui_nicht_mehr_noetig_ist(): void
    {
        $text = $this->lies('README.md');

        $this->assertStringContainsString('initCombobox', $text);
        $this->assertMatchesRegularExpression('/jquery[ -]?ui[^\n]*nicht mehr|nicht mehr[^\n]*jquery[ -]?ui/i', $text);
    }
}
