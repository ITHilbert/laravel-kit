<?php

namespace ITHilbert\LaravelKit\Helpers;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Stellt HTML Buttons zur Verfügung.
 * Wird beötigt, wenn die VUE Componenten nicht funktionieren, z.B. in der DataTable.
 *
 * Alle Werte werden wie e() escaped; gewolltes HTML im Text oder Tooltip als Htmlable (z. B. HtmlString) übergeben.
 * Schlüssel in $attributes, die ein fest gesetztes Attribut doppeln, entfallen.
 */
class HButton
{
    use HtmlAusgabe;

    /** Fest gesetzte Attribute der Link-Buttons (project, create, show, edit, back). */
    private const FEST_LINK = ['href', 'class', 'data-toggle', 'title'];

    /** Fest gesetzte Attribute von delete. */
    private const FEST_DELETE = ['onclick', 'data-kit-delete', 'data-toggle', 'title', 'class'];

    /** Fest gesetzte Attribute von submit und save. */
    private const FEST_SUBMIT = ['type', 'class', 'data-toggle', 'title'];

    /**
     * Erstellt einen project Button
     *
     * @param  string|null  $route  Route für den Button
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function project($route = '#', $text = 'Projete', $tooltip = 'Projekte', $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_LINK);

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-create  '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.' ><i class="fas fa-parking"></i> '.self::esc($text).'</a>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen create Button
     *
     * @param  string|null  $route  Route für den Button
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function create($route = '#', $text = 'Erstellen', $tooltip = 'Erstellen', $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_LINK);

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-create  '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'><i class="fas fa-plus-circle"></i> '.self::esc($text).'</a>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen show Button
     *
     * @param  string|null  $route  Route für den Button
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function show($route = '#', $text = 'Anzeigen', $tooltip = 'Anzeigen', $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_LINK);

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-show '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'><i class="fas fa-file-contract"></i> '.self::esc($text).'</a>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen edit Button ($class wird nicht ausgegeben)
     *
     * @param  string|null  $route  Route für den Button
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  wird nicht verwendet
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function edit($route = '#', $text = 'Bearbeiten', $tooltip = 'Bearbeiten', $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_LINK);

        $ausgabe = '<a href="'.self::esc($route).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" class="btn btn-edit" '.$attr.'><i class="fas fa-pencil-alt"></i> '.self::esc($text).'</a>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen delete Button
     *
     * Der Klick öffnet den Lösch-Dialog über data-kit-delete; eine globale Funktion OpenDeleteForm wird
     * nur aufgerufen, wenn es sie gibt.
     *
     * @param  int|string|null  $id  ID des Datensatzes
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function delete($id, $text = new HtmlString('L&ouml;schen'), $tooltip = new HtmlString('L&ouml;schen'), $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_DELETE);

        $onclick = "if(typeof OpenDeleteForm==='function'){OpenDeleteForm(".self::alsJavaScript($id).')}';

        $ausgabe = '<button onclick="'.self::esc($onclick).'" data-kit-delete="'.self::esc((string) $id).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" class="btn btn-delete '.self::esc($class).'" '.$attr.'><i class="fas fa-minus-circle"></i> '.self::esc($text).'</button>'."\n";

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen submit Button
     *
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function submit($text = 'Speichern', $tooltip = 'Speichern', $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_SUBMIT);

        return self::toHtmlString('<button type="submit" class="btn btn-submit '.self::esc($class).'" '.$attr.' data-toggle="tooltip" title="'.self::esc($tooltip).'">'.self::esc($text).'</button>');
    }

    /**
     * Erstellt einen save Button
     *
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function save($text = 'Speichern', $tooltip = 'Speichern', $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_SUBMIT);

        $ausgabe = '<button type="submit" class="btn btn-save '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'><i class="fas fa-save"></i> '.self::esc($text).'</button>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen back Button
     *
     * @param  string|null  $route  Route für den Button
     * @param  string|Htmlable|null  $text  Bezeichnung für den Button
     * @param  string|Htmlable|null  $tooltip  Tooltip
     * @param  string|null  $class  weitere CSS Classes
     * @param  array<array-key, mixed>  $attributes  andere Attribute
     * @return HtmlString
     */
    public static function back($route = '#', $text = new HtmlString('Zur&uuml;ck'), $tooltip = new HtmlString('Zur&uuml;ck'), $class = '', array $attributes = [])
    {
        $attr = self::attributes($attributes, self::FEST_LINK);

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-back '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'><i class="fas fa-undo"></i> '.self::esc($text).'</a>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt die ID als JavaScript-Wert aus: endliche Zahl als Zahl, alles andere als JSON-Zeichenkette
     * (null ergibt ""). Das Ergebnis enthält kein <, >, &, ' oder ".
     */
    private static function alsJavaScript(mixed $id): string
    {
        return json_encode(
            is_int($id) || (is_float($id) && is_finite($id)) ? $id : (string) $id,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
        );
    }
}
