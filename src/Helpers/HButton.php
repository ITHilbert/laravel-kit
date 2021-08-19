<?php
namespace ITHilbert\LaravelKit\Helpers;

use Illuminate\Support\HtmlString;


/**
 * Stellt HTML Buttons zur Verfügung.
 * Wird beötigt, wenn die VUE Componenten nicht funktionieren, z.B. in der DataTable.
 */
class HButton{


    public static function project( $route='#', $text= 'Projete', $tooltip = 'Projekte', $class='', array $attributes = []){
        //Weitere Attribute setzen
       $attr = self::attributes($attributes);

       $ausgabe = '<a href="'.$route.'" class="btn btn-create  '.$class.'" data-toggle="tooltip" title="'.$tooltip.'" '.$attr.' ><i class="fas fa-parking"></i> '.$text.'</a>';
       return self::toHtmlString($ausgabe);
    }



    /**
     * Erstellt einen create Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function create( $route='#', $text= 'Erstellen', $tooltip = 'Erstellen', $class='', array $attributes = []){
        //Weitere Attribute setzen
       $attr = self::attributes($attributes);


       $ausgabe = '<a href="'.$route.'" class="btn btn-create  '.$class.'" data-toggle="tooltip" title="'.$tooltip.'" '.$attr.'><i class="fas fa-plus-circle"></i> '.$text.'</a>';
       return self::toHtmlString($ausgabe);
    }


    /**
     * Erstellt einen show Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function show( $route='#', $text= 'Anzeigen', $tooltip = 'Anzeigen', $class='', array $attributes = []){
         //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<a href="'.$route.'" class="btn btn-show '.$class.'" data-toggle="tooltip" title="'.$tooltip.'" '.$attr.'><i class="fas fa-file-contract"></i> '.$text.'</a>';
        return self::toHtmlString($ausgabe);
    }


    /**
     * Erstellt einen edit Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function edit( $route='#', $text= 'Bearbeiten', $tooltip = 'Bearbeiten', $class='', array $attributes = []){
         //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<a href="'.$route.'" data-toggle="tooltip" title="'.$tooltip.'" class="btn btn-edit" '.$attr.'><i class="fas fa-pencil-alt"></i> '.$text.'</a>';

        return self::toHtmlString($ausgabe);
    }

    /**
     * Erstellt einen delete Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function delete($id,  $text= 'L&ouml;schen', $tooltip = 'L&ouml;schen', $class='', array $attributes = []){
         //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<button onclick="OpenDeleteForm('.$id.')" data-toggle="tooltip" title="'.$tooltip.'" class="btn btn-delete '.$class.'" '.$attr.'><i class="fas fa-minus-circle"></i> '.$text.'</button>' . "\n";

        return self::toHtmlString($ausgabe);
    }


    /**
     * Erstellt einen submit Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function submit($text= 'Speichern', $tooltip = 'Speichern', $class='', array $attributes = []){
         //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        return self::toHtmlString('<button type="submit" class="btn btn-submit '.$class.'" '.$attr.' data-toggle="tooltip" title="'.$tooltip.'">'.$text.'</button>');
    }


    /**
     * Erstellt einen save Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function save($text= 'Speichern', $tooltip = 'Speichern', $class='', array $attributes = []){
         //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<button type="submit" class="btn btn-save '.$class.'" data-toggle="tooltip" title="'.$tooltip.'" '.$attr.'><i class="fas fa-save"></i> '.$text.'</button>';

        return self::toHtmlString($ausgabe);
    }


    /**
     * Erstellt einen back Button
     *
     * @param string $route Route für den Button
     * @param string $text Bezeichnunng für den Button
     * @param string $class weitere CSS Classes
     * @param array $attributes andere Attribute
     * @return void
     */
    public static function back($route='#', $text= 'Zur&uuml;ck', $tooltip = 'Zur&uuml;ck', $class='', array $attributes = []){
         //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<a href="'.$route.'" class="btn btn-back '.$class.'" data-toggle="tooltip" title="'.$tooltip.'" '.$attr.'><i class="fas fa-undo"></i> '.$text.'</a>';

        return self::toHtmlString($ausgabe);
    }


    /**
     * Formatiert einen String in HTML Code
     *
     * @param type $html
     * @return HtmlString
     */
    protected static function toHtmlString($html)
    {
        return new HtmlString($html);
    }





       /**
     * Formatiert die Werte im Array zu HTML Attributten
     *
     * @param array $options
     * @return string
     */
    protected static function attributes(array $options = []){
        $attr = '';
        foreach ($options as $key => $value) {
            $attr .= $key .'="' . $value .'" ';
        }
        return $attr;
    }
}
