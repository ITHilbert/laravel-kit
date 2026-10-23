<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\Breadcrumb;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Charakterisierung der Breadcrumb-Hilfsklasse (Ist-Stand).
 */
final class BreadcrumbTest extends TestCase
{
    // Charakterisierung
    public function test_wurzel_verlinkt_auf_startseite_und_nutzt_config_namen(): void
    {
        config(['laravelkit.breadcrumb.root_name' => 'Meine Seite']);

        $items = (new Breadcrumb)->getItems();

        $this->assertSame([['url' => '/', 'title' => 'Meine Seite']], $items);
    }

    // Charakterisierung
    public function test_wurzel_aktiv_hat_keinen_link(): void
    {
        config(['laravelkit.breadcrumb.root_name' => 'Home']);

        $items = (new Breadcrumb(true))->getItems();

        $this->assertSame([['url' => '#', 'title' => 'Home']], $items);
    }

    // Charakterisierung
    public function test_add_haengt_eintraege_der_reihe_nach_an(): void
    {
        $breadcrumb = new Breadcrumb;
        $breadcrumb->add('Benutzer', '/users');
        $breadcrumb->add('Bearbeiten');

        $items = $breadcrumb->getItems();

        $this->assertCount(3, $items);
        $this->assertSame(['url' => '/users', 'title' => 'Benutzer'], $items[1]);
        $this->assertSame(['url' => '#', 'title' => 'Bearbeiten'], $items[2]);
    }

    // Charakterisierung
    public function test_config_wert_null_ergibt_leeren_titel_nicht_den_default(): void
    {
        config(['laravelkit.breadcrumb.root_name' => null]);

        // Ist-Verhalten: config() liefert bei vorhandenem Schluessel mit null den Wert null, nicht 'Startseite'.
        $this->assertNull((new Breadcrumb)->getItems()[0]['title']);
    }
}
