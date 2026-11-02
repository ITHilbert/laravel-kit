<?php

namespace ITHilbert\LaravelKit\Helpers;

use Illuminate\Support\Facades\URL;

/**
 * Diese Klasse erstellt Textboxen
 * https://getbootstrap.com/docs/4.0/components/input-group/
 *
 *
 * Erweitern:
 * =========
 * Element Form
 * Header für die Funktionen
 * ComboBox und Listbox bearbeiten
 * OldValue einarbeiten
 *

@version 1.0.0
 **/
class HForm
{
    use HtmlAusgabe;

    /**
     * Erstellt ein Form Tag
     *
     * @param  type  $action
     * @param  type  $method
     * @param  type  $file
     * @param  array  $options
     * @return HTML
     */
    public static function open($action, $method = 'post', $file = false, array $attributes = [])
    {
        $fest = ['id', 'method', 'action'];
        if ($file !== false) {
            $file = 'enctype="multipart/form-data"';
            $fest[] = 'enctype';
        } else {
            $file = '';
        }

        $attr = self::attributes($attributes, $fest);

        $ausgabe = '<form id="myform" method="'.self::esc($method).'" action="'.self::esc($action).'" '.$file.' '.$attr.'>'."\n".csrf_field();

        return self::toHtmlString($ausgabe);
    }

    /**
     * Beendet ein Form Tag
     *
     * @return HTML
     */
    public static function close()
    {
        return self::toHtmlString('</form>');
    }

    /**
     * Gibt ein Script zurück
     *
     * @param  type  $pfad
     * @return HTML
     */
    public static function script($pfad)
    {
        $ausgabe = '<script src="'.URL::asset('/').self::esc($pfad).'" ></script>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein CSS Style zurück.
     *
     * @param  type  $pfad
     * @return HTML
     */
    public static function style($pfad)
    {
        $ausgabe = '<link href="'.URL::asset('/').self::esc($pfad).'" rel="stylesheet">';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein URL zum public Ordner zurück.
     *
     * @param  type  $pfad
     * @return HTML
     */
    public static function public($pfad)
    {
        $ausgabe = URL::asset('/').self::esc($pfad);

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für die Text eingabe zurück.
     *
     * @param  type  $name
     * @param  type  $value
     * @param  type  $class
     * @return HTML
     */
    public static function text($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        $value = 'value="'.self::esc($value).'"';

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'value']);

        $ausgabe = '<input type="text" name="'.self::esc($name).'" class="form-control input-text '.self::esc($class).'" '.$value.' '.$attr.' >';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein verstecktes Input Feld zurück
     *
     * @param  type  $name
     * @param  type  $value
     * @return HTML
     */
    public static function hidden($name, $value = '', array $attributes = [])
    {
        // Value Attribute setzen
        $value = 'value="'.self::esc($value).'"';

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'value']);

        $ausgabe = '<input type="hidden" name="'.self::esc($name).'" '.$value.' '.$attr.' />';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Euro eingabe zurück,
     * mit 2 Nachkommastellen.
     *
     * @param  type  $name
     * @param  float  $value
     * @param  type  $class
     * @return type
     */
    public static function euro($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if (is_numeric($value)) {
            $value2 = 'value="'.self::esc(number_format($value, 2, ',', '.')).'"';
        } else {
            $value2 = 'value="'.self::esc($value).'"';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'id', 'name', 'class', 'aria-label', 'value']);

        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" id="'.self::esc($name).'" name="'.self::esc($name).'" class="form-control input-euro '.self::esc($class).'" aria-label="Euro" '.$value2.' '.$attr.' >';
        $ausgabe .= '<div class="input-group-append">';
        $ausgabe .= '<span class="input-group-text"> &euro; </span>';
        $ausgabe .= '</div>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Prozent eingabe zurück,
     * mit 0 Nachkommastellen.
     *
     * @param  type  $name
     * @param  float  $value
     * @param  type  $class
     * @return type
     */
    public static function prozent($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if (is_numeric($value)) {
            $value = 'value="'.self::esc(number_format($value, 0, ',', '.')).'"';
        } else {
            $value = 'value="'.self::esc($value).'"';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'aria-label', 'value']);

        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" name="'.self::esc($name).'" class="form-control input-prozent '.self::esc($class).'" aria-label="Prozent" '.$value.' '.$attr.'>';
        $ausgabe .= '<div class="input-group-append">';
        $ausgabe .= '<span class="input-group-text">%</span>';
        $ausgabe .= '</div>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Prozent eingabe zurück,
     * mit 1 Nachkommastellen.
     *
     * @param  type  $name
     * @param  float  $value
     * @param  type  $class
     * @return type
     */
    public static function prozent1($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if (is_numeric($value)) {
            $value = 'value="'.self::esc(number_format($value, 1, ',', '.')).'"';
        } else {
            $value = 'value="'.self::esc($value).'"';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'aria-label', 'value']);

        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" name="'.self::esc($name).'" class="form-control input-prozent '.self::esc($class).'" aria-label="Prozent" '.$value.' '.$attr.'>';
        $ausgabe .= '<div class="input-group-append">';
        $ausgabe .= '<span class="input-group-text">%</span>';
        $ausgabe .= '</div>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Prozent eingabe zurück,
     * mit 2 Nachkommastellen.
     *
     * @param  type  $name
     * @param  float  $value
     * @param  type  $class
     * @return type
     */
    public static function prozent2($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if (is_numeric($value)) {
            $value = 'value="'.self::esc(number_format($value, 2, ',', '.')).'"';
        } else {
            $value = 'value="'.self::esc($value).'"';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'aria-label', 'value']);

        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" name="'.self::esc($name).'" class="form-control input-prozent '.self::esc($class).'" aria-label="Prozent" '.$value.' '.$attr.'>';
        $ausgabe .= '<div class="input-group-append">';
        $ausgabe .= '<span class="input-group-text">%</span>';
        $ausgabe .= '</div>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Integer eingabe zurück,
     *
     * @param  type  $name
     * @param  type  $value
     * @param  type  $class
     * @return type
     */
    public static function int($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if (is_numeric($value)) {
            $value = 'value="'.self::esc(number_format($value, 0, ',', '.')).'"';
        } else {
            $value = 'value="'.self::esc($value).'"';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'value']);

        $ausgabe = '<input type="text" name="'.self::esc($name).'" class="form-control input-int '.self::esc($class).'" '.$value.' '.$attr.'>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Zahlen eingabe zurück.
     *
     * @param  type  $name
     * @param  type  $value
     * @param  int  $nachkomma
     * @param  type  $class
     * @return type
     */
    public static function number($name = '', $value = '', $nachkomma = 0, $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if (is_numeric($value)) {
            $value = 'value="'.self::esc(number_format($value, $nachkomma, ',', '.')).'"';
        } else {
            $value = 'value="'.self::esc($value).'"';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'value']);

        $ausgabe = '<input type="text" name="'.self::esc($name).'" class="form-control input-zahl  '.self::esc($class).'" '.$value.' '.$attr.' >';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Datum eingabe zurück.
     *
     * @param  type  $name
     * @param  type  $value
     * @param  type  $class
     * @return type
     */
    public static function date($name = '', $value = '', $class = '', array $attributes = [])
    {
        // Value Attribute setzen
        if ($value != '') {
            $datum = new MyDateTime($value);
            $value = $datum->getDateDE();
        }
        $value = 'value="'.self::esc($value).'"';

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'value']);

        $ausgabe = '<input type="text" name="'.self::esc($name).'" class="form-control input-date '.self::esc($class).'" '.$value.' '.$attr.' >';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein Input Feld für Passwort eingabe zurück.
     *
     * @param  type  $name
     * @param  type  $class
     * @return type
     */
    public static function password($name = '', $class = '', array $attributes = [])
    {
        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['type', 'name', 'class', 'autocomplete']);

        $ausgabe = '<input type="password" name="'.self::esc($name).'" class="form-control input-passw '.self::esc($class).'" '.$attr.' autocomplete="new-password">';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Git eine TextArea zurück
     *
     * @param  type  $name
     * @param  type  $value
     * @param  type  $class
     * @param  type  $rows
     * @return type
     */
    public static function textArea($name = '', $value = '', $class = '', $rows = 3, array $attributes = [])
    {
        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['id', 'name', 'class', 'rows']);

        $ausgabe = '<textarea id="'.self::esc($name).'" name="'.self::esc($name).'" class="form-control '.self::esc($class).'" rows="'.self::esc($rows).'" '.$attr.'>'.self::esc($value).'</textarea>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Liefert ein Label zurück
     *
     * @param  type  $label
     * @param  type  $forID
     * @param  type  $class
     * @return type
     */
    public static function label($label, $forID = '', $class = '', array $attributes = [])
    {
        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['class', 'for']);

        $ausgabe = '<label class="form-check-label '.self::esc($class).'" for="'.self::esc($forID).'"'.($attr !== '' ? ' '.rtrim($attr) : '').'>'.self::esc($label).'</label>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt eine CheckBox zurück
     *
     * @param  type  $name
     * @param  type  $label
     * @param  type  $value
     * @param  type  $checked
     * @param  type  $inline
     * @param  type  $class
     * @return type
     */
    public static function checkBox($name = '', $label = '', $value = '', $checked = false, $inline = false, $class = '', array $attributes = [])
    {
        // id aus $attributes in beliebiger Schreibweise (die erste gilt), sonst der Name
        $id = '';
        foreach ($attributes as $key => $wert) {
            if (strtolower((string) $key) === 'id') {
                $id = $wert ?? '';
                break;
            }
        }
        if ((string) $id === '') {
            $id = $name ?? '';
        }

        $fest = ['type', 'name', 'id', 'class', 'value'];
        if ($checked == false) {
            $checked = '';
        } else {
            $checked = 'checked';
            $fest[] = 'checked';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, $fest);
        $idAttr = (string) $id !== '' ? ' id="'.self::esc($id).'"' : '';
        $forAttr = ' for="'.self::esc($id).'"';

        $ausgabe = '';
        if ($inline == false) {
            $ausgabe .= '<div class="form-check">';
        } else {
            $ausgabe .= '<div class="form-check form-check-inline">';
        }

        $ausgabe .= '<input type="checkbox" name="'.self::esc($name).'"'.$idAttr.' class="form-check-input '.self::esc($class).'" value="'.self::esc($value).'" '.$checked.' '.$attr.'>';
        $ausgabe .= '<label class="form-check-label"'.$forAttr.'>'.self::esc($label).'</label>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt eine RadioBox zurück
     *
     * @param  type  $id
     * @param  type  $group
     * @param  type  $label
     * @param  type  $value
     * @param  type  $checked
     * @param  type  $inline
     * @param  type  $class
     * @return type
     */
    public static function radioBox($id = '', $group = '', $label = '', $value = '', $checked = false, $inline = false, $class = '', array $attributes = [])
    {
        $fest = ['type', 'id', 'name', 'class', 'value'];
        if ($checked == false) {
            $checked = '';
        } else {
            $checked = 'checked';
            $fest[] = 'checked';
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, $fest);

        $ausgabe = '';
        if ($inline == false) {
            $ausgabe .= '<div class="form-check">';
        } else {
            $ausgabe .= '<div class="form-check form-check-inline">';
        }
        $ausgabe .= '<input type="radio" id="'.self::esc($id).'" name="'.self::esc($group).'" class="form-check-input '.self::esc($class).'" value="'.self::esc($value).'" '.$checked.' '.$attr.'>';
        $ausgabe .= '<label class="form-check-label" for="'.self::esc($id).'">'.self::esc($label).'</label>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt eine ComboBox zurück
     *
     * @param  type  $name
     * @param  object  $daten
     * @param  type  $selectedID
     * @param  type  $firstEntry
     * @param  type  $class
     * @return type
     */
    public static function comboBox($name, $daten = null, $selectedID = 0, $firstEntry = '', $class = '', array $attributes = [])
    {
        // SellectedID ermitteln
        if ($firstEntry == '' && $selectedID == 0) {
            $selectedID = 1;
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['name', 'class']);

        $ausgabe = '';

        $ausgabe = '<select name="'.self::esc($name).'" class="form-control '.self::esc($class).'" '.$attr.'>'."\n";

        if ($firstEntry != '' && $selectedID == 0) {
            $ausgabe .= '<option value="0" selected>'.self::esc($firstEntry).'</option>'."\n";
        } elseif ($firstEntry != '') {
            $ausgabe .= '<option value="0">'.self::esc($firstEntry).'</option>'."\n";
        }

        foreach ($daten as $row) {
            if ($selectedID == $row->getFieldID()) {
                $ausgabe .= '<option value="'.self::esc($row->getFieldID()).'" selected>'.self::esc($row->getFieldBez()).'</option>'."\n";
            } else {
                $ausgabe .= '<option value="'.self::esc($row->getFieldID()).'">'.self::esc($row->getFieldBez()).'</option>'."\n";
            }
        }

        $ausgabe .= '</select>'."\n";

        return self::toHtmlString($ausgabe);
    }

    /**
     * Liefert eine ListBox zurück
     *
     * @param  type  $name
     * @param  object  $daten
     * @param  type  $selectedID
     * @param  type  $size
     * @param  type  $class
     * @return type
     */
    public static function listBox($name, $daten = null, $selectedID = 1, $size = 5, $class = '', array $attributes = [])
    {

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['multiple', 'size', 'name', 'class']);

        $ausgabe = '<select multiple size="'.self::esc($size).'" name="'.self::esc($name).'" class="form-control '.self::esc($class).'" '.$attr.'>'."\n";
        foreach ($daten as $row) {
            if ($selectedID == $row->getFieldID()) {
                $ausgabe .= '<option value="'.self::esc($row->getFieldID()).'" selected>'.self::esc($row->getFieldBez()).'</option>'."\n";
            } else {
                $ausgabe .= '<option value="'.self::esc($row->getFieldID()).'">'.self::esc($row->getFieldBez()).'</option>'."\n";
            }
        }
        $ausgabe .= '</select>'."\n";

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt eine ComboBox zurück
     *
     * @param  type  $name
     * @param  array  $daten
     * @param  type  $selectedID
     * @param  type  $firstEntry
     * @param  type  $class
     * @return type
     */
    public static function comboBoxArray($name, $daten = [], $selectedID = 0, $firstEntry = '', $class = '', array $attributes = [])
    {
        // SellectedID ermitteln
        if ($firstEntry == '' && $selectedID == 0) {
            $selectedID = 1;
        }

        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['id', 'name', 'class']);

        $ausgabe = '<select id="'.self::esc($name).'" name="'.self::esc($name).'" class="form-control '.self::esc($class).'" '.$attr.'>'."\n";

        if ($firstEntry != '' && $selectedID == 0) {
            $ausgabe .= '<option value="0" selected>'.self::esc($firstEntry).'</option>'."\n";
        } elseif ($firstEntry != '') {
            $ausgabe .= '<option value="0">'.self::esc($firstEntry).'</option>'."\n";
        }

        foreach ($daten as $row) {
            if ($selectedID == $row[0]) {
                $ausgabe .= '<option value="'.self::esc($row[0]).'" selected>'.self::esc($row[1]).'</option>'."\n";
            } else {
                $ausgabe .= '<option value="'.self::esc($row[0]).'">'.self::esc($row[1]).'</option>'."\n";
            }
        }
        $ausgabe .= '</select>'."\n";

        return self::toHtmlString($ausgabe);
    }

    /**
     * Liefert eine ListBox zurück
     *
     * @param  type  $name
     * @param  array  $daten
     * @param  type  $selectedID
     * @param  type  $size
     * @param  type  $class
     * @return type
     */
    public static function listBoxArray($name, $daten = [], $selectedID = 0, $size = 5, $class = '', array $attributes = [])
    {
        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['multiple', 'size', 'name', 'class']);

        $ausgabe = '<select multiple size="'.self::esc($size).'" name="'.self::esc($name).'" class="form-control '.self::esc($class).'" '.$attr.'>'."\n";
        foreach ($daten as $row) {
            if ($selectedID == $row[0]) {
                $ausgabe .= '<option value="'.self::esc($row[0]).'" selected>'.self::esc($row[1]).'</option>'."\n";
            } else {
                $ausgabe .= '<option value="'.self::esc($row[0]).'">'.self::esc($row[1]).'</option>'."\n";
            }
        }
        $ausgabe .= '</select>'."\n";

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt eine Div die die Elemente Horizontal anordnet.
     *
     * @param  type  $class
     * @return type
     */
    public static function hGroupStart($class = 'mb-3', array $attributes = [])
    {
        // Weitere Attribute setzen
        $attr = self::attributes($attributes, ['class']);

        $ausgabe = '<div class="input-group '.self::esc($class).'" '.$attr.'>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Beendet die Horizontal Div
     *
     * @return type
     */
    public static function hGroupEnde()
    {
        return self::toHtmlString('</div>'."\n");
    }

    public static function com($comment)
    {
        return self::toHtmlString('<!-- '.self::esc($comment).' -->');
    }

    public static function comStart()
    {
        return self::toHtmlString('<!-- ');
    }

    public static function comEnde()
    {
        return self::toHtmlString(' -->');
    }
} // Class Ende
