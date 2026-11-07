<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Kit\KitFeld;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * kit-blade-formularfelder (#921): reine Ableitungen der Hilfsklasse KitFeld ohne App.
 *
 * API (vom Test festgelegt):
 * - KitFeld::punkt(string $name): string — Feldname in Punktschreibweise für old() und den $errors-Bag.
 * - KitFeld::id(string $name, ?string $id = null): string — übergebene id, sonst die id aus dem Namen.
 * App-abhängige Teile (alt(), fehler()) prüft tests/Feature/KitComponents/FormularNeuTest.php.
 */
final class KitFeldTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public static function punktFaelle(): array
    {
        return [
            'einfacher Name' => ['name', 'name'],
            'eine Ebene' => ['adresse[ort]', 'adresse.ort'],
            'Liste' => ['tags[]', 'tags'],
            'zwei Ebenen' => ['a[b][c]', 'a.b.c'],
            'leer' => ['', ''],
        ];
    }

    // AK5
    #[DataProvider('punktFaelle')]
    public function test_punkt_bildet_array_schreibweise_auf_punktschreibweise_ab(string $name, string $erwartet): void
    {
        $this->assertSame($erwartet, KitFeld::punkt($name));
    }

    /** @return array<string, array{string, string}> */
    public static function idFaelle(): array
    {
        return [
            'einfacher Name' => ['name', 'name'],
            'eine Ebene' => ['adresse[ort]', 'adresse_ort'],
            'Liste' => ['tags[]', 'tags'],
            'zwei Ebenen' => ['a[b][c]', 'a_b_c'],
        ];
    }

    // Randfall 2
    #[DataProvider('idFaelle')]
    public function test_id_ohne_uebergebene_id_kommt_aus_dem_namen(string $name, string $erwartet): void
    {
        $this->assertSame($erwartet, KitFeld::id($name));
        $this->assertSame($erwartet, KitFeld::id($name, null));
    }

    // Randfall 2
    public function test_id_mit_leer_uebergebener_id_kommt_aus_dem_namen(): void
    {
        $this->assertSame('adresse_ort', KitFeld::id('adresse[ort]', ''));
    }

    // Randfall 2
    #[DataProvider('idFaelle')]
    public function test_uebergebene_id_geht_vor(string $name, string $abgeleitet): void
    {
        $this->assertNotSame('eigene_id', $abgeleitet);
        $this->assertSame('eigene_id', KitFeld::id($name, 'eigene_id'));
    }
}
