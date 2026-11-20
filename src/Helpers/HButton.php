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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-create  '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.' >'.self::icon('project').' '.self::esc($text).'</a>';

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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-create  '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'>'.self::icon('create').' '.self::esc($text).'</a>';

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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-show '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'>'.self::icon('show').' '.self::esc($text).'</a>';

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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $ausgabe = '<a href="'.self::esc($route).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" class="btn btn-edit" '.$attr.'>'.self::icon('edit').' '.self::esc($text).'</a>';

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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $onclick = "if(typeof OpenDeleteForm==='function'){OpenDeleteForm(".self::alsJavaScript($id).')}';

        $ausgabe = '<button onclick="'.self::esc($onclick).'" data-kit-delete="'.self::esc((string) $id).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" class="btn btn-delete '.self::esc($class).'" '.$attr.'>'.self::icon('delete').' '.self::esc($text).'</button>'."\n";

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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $ausgabe = '<button type="submit" class="btn btn-save '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'>'.self::icon('save').' '.self::esc($text).'</button>';

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
        $attr = self::ariaLabel($text, $tooltip, $attributes).$attr;

        $ausgabe = '<a href="'.self::esc($route).'" class="btn btn-back '.self::esc($class).'" data-toggle="tooltip" title="'.self::esc($tooltip).'" '.$attr.'>'.self::icon('back').' '.self::esc($text).'</a>';

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

    /**
     * Inline-SVG-Icon (Pfade wie vue/tailwind/buttons/button-*.vue).
     */
    private static function icon(string $name): string
    {
        $pfade = [
            'project' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
            'create' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
            'show' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
            'edit' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
            'delete' => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
            'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>',
            'back' => '<polyline points="15 18 9 12 15 6"/>',
        ];

        return '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$pfade[$name].'</svg>';
    }

    /**
     * aria-label mit dem Tooltip, wenn der Text leer ist (nach trim) und der Aufrufer keins übergibt.
     *
     * @param  array<array-key, mixed>  $attributes
     */
    private static function ariaLabel(mixed $text, mixed $tooltip, array $attributes): string
    {
        $inhalt = $text instanceof Htmlable ? $text->toHtml() : (string) $text;
        if (trim($inhalt) !== '') {
            return '';
        }

        foreach (array_keys($attributes) as $schluessel) {
            if (strtolower((string) $schluessel) === 'aria-label') {
                return '';
            }
        }

        return 'aria-label="'.self::esc($tooltip).'" ';
    }
}
