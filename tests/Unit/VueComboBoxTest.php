<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ITHilbert\LaravelKit\Entities\ComboBox;
use ITHilbert\LaravelKit\Tests\TestCase;
use ITHilbert\LaravelKit\Traits\VueComboBox;

/**
 * Charakterisierung des Traits VueComboBox (Ist-Stand), genutzt von userauth.
 */
final class VueComboBoxTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('combo_modelle', function (Blueprint $table): void {
            $table->id();
            $table->string('caption');
        });
    }

    // Charakterisierung
    public function test_attribute_cb_key_und_cb_caption_stehen_zur_verfuegung(): void
    {
        $modell = ComboModell::create(['caption' => 'Eins']);

        $this->assertSame($modell->id, $modell->cbKey);
        $this->assertSame('Eins', $modell->cbCaption);
    }

    // Charakterisierung
    public function test_attribute_werden_in_array_und_json_ausgegeben(): void
    {
        $modell = ComboModell::create(['caption' => 'Eins']);

        $array = $modell->toArray();

        $this->assertSame($modell->id, $array['cbKey']);
        $this->assertSame('Eins', $array['cbCaption']);
    }

    // Charakterisierung: Massenzuweisung im Konstruktor bleibt erhalten (Kommentar im Trait)
    public function test_massenzuweisung_im_konstruktor_geht_nicht_verloren(): void
    {
        $modell = new ComboModell(['caption' => 'Zwei']);

        $this->assertSame('Zwei', $modell->caption);
    }

    // Charakterisierung
    public function test_combo_box_data_liefert_json_liste_aller_datensaetze(): void
    {
        $a = ComboModell::create(['caption' => 'A']);
        $b = ComboModell::create(['caption' => 'B']);

        $json = ComboModell::getComboBoxData();

        $this->assertSame([
            ['cbKey' => $a->id, 'cbCaption' => 'A'],
            ['cbKey' => $b->id, 'cbCaption' => 'B'],
        ], json_decode($json, true));
    }

    // Charakterisierung
    public function test_combo_box_data_ohne_datensaetze_ist_leeres_json_array(): void
    {
        $this->assertSame('[]', ComboModell::getComboBoxData());
    }

    // Charakterisierung
    public function test_combo_box_entity_hat_oeffentliche_felder(): void
    {
        $cb = new ComboBox;
        $cb->cbKey = 1;
        $cb->cbCaption = 'x';

        $this->assertSame('{"cbKey":1,"cbCaption":"x"}', json_encode($cb));
    }
}

final class ComboModell extends Model
{
    use VueComboBox;

    protected $table = 'combo_modelle';

    protected $fillable = ['caption'];

    public $timestamps = false;
}
