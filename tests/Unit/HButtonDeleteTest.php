<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use DOMDocument;
use DOMXPath;
use ITHilbert\LaravelKit\Helpers\HButton;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * HButton::delete($id): Marker data-kit-delete für die Kit-Insel, onclick bleibt.
 */
final class HButtonDeleteTest extends TestCase
{
    private function button(string $html): \DOMElement
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>');
        libxml_clear_errors();
        $knoten = (new DOMXPath($dom))->query('//button');
        $this->assertSame(1, $knoten->length, $html);

        return $knoten->item(0);
    }

    // AK5
    public function test_delete_gibt_data_kit_delete_mit_der_id_aus(): void
    {
        $button = $this->button((string) HButton::delete(42));

        $this->assertTrue($button->hasAttribute('data-kit-delete'));
        $this->assertSame('42', $button->getAttribute('data-kit-delete'));
    }

    // AK5
    public function test_delete_mit_textid_gibt_data_kit_delete_mit_dieser_id_aus(): void
    {
        $button = $this->button((string) HButton::delete('17'));

        $this->assertSame('17', $button->getAttribute('data-kit-delete'));
        $this->assertStringContainsString('OpenDeleteForm', $button->getAttribute('onclick'));
    }

    // AK5
    public function test_delete_behaelt_onclick_open_delete_form(): void
    {
        $button = $this->button((string) HButton::delete(42));

        $this->assertStringContainsString('OpenDeleteForm', $button->getAttribute('onclick'));
    }

    // AK5
    public function test_delete_behaelt_klassen_text_und_weitere_attribute(): void
    {
        $button = $this->button((string) HButton::delete(5, 'Weg', 'Tip', 'extra', ['data-x' => 'y']));

        $this->assertStringContainsString('btn btn-delete', $button->getAttribute('class'));
        $this->assertStringContainsString('extra', $button->getAttribute('class'));
        $this->assertSame('Tip', $button->getAttribute('title'));
        $this->assertSame('y', $button->getAttribute('data-x'));
        $this->assertSame('5', $button->getAttribute('data-kit-delete'));
        $this->assertStringContainsString('OpenDeleteForm', $button->getAttribute('onclick'));
        $this->assertStringContainsString('Weg', $button->textContent);
    }

    // AK5
    public function test_delete_gibt_je_id_eigenen_marker_aus(): void
    {
        $a = $this->button((string) HButton::delete(1));
        $b = $this->button((string) HButton::delete(2));

        $this->assertSame('1', $a->getAttribute('data-kit-delete'));
        $this->assertSame('2', $b->getAttribute('data-kit-delete'));
    }
}
