<?php
namespace ITHilbert\LaravelKit\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\URL;

use ithilbert\LaravelKit\MyDateTime;

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
class HForm{



    /**
     * Erstellt ein Form Tag
     *
     * @param type $action
     * @param type $method
     * @param type $file
     * @param array $options
     * @return HTML
     */
    public static function open($action, $method = 'post', $file = false, array $attributes = []){
        if($file !== false){
            $file = 'enctype="multipart/form-data"';
        }else{
            $file = '';
        }

        $attr = self::attributes($attributes);

        $ausgabe = '<form id="myform" method="'.$method.'" action="'. $action .'" '.$file.' '.$attr.'>' . "\n". csrf_field();
        return self::toHtmlString($ausgabe);
    }


    /**
     * Beendet ein Form Tag
     *
     * @return HTML
     */
    public static function close(){
        return self::toHtmlString('</form>');
    }


    /**
     * Gibt ein Script zurück
     *
     * @param type $pfad
     * @return HTML
     */
    public static function script($pfad){
        $ausgabe = '<script src="'. URL::asset('/') . $pfad .'" ></script>';
        return self::toHtmlString($ausgabe);
    }

    /**
     * Gibt ein CSS Style zurück.
     *
     * @param type $pfad
     * @return HTML
     */
    public static function style($pfad){
        $ausgabe = '<link href="'. URL::asset('/') . $pfad .'" rel="stylesheet">';
        return self::toHtmlString($ausgabe);
    }


    /**
     * Gibt ein URL zum public Ordner zurück.
     *
     * @param type $pfad
     * @return HTML
     */
    public static function public($pfad){
        $ausgabe = URL::asset('/') . $pfad;
        return self::toHtmlString($ausgabe);
    }


    /**
     * Gibt ein Input Feld für die Text eingabe zurück.
     *
     * @param type $name
     * @param type $value
     * @param type $class
     * @param array $attributes
     * @return HTML
     */
    public static function text($name='', $value ='', $class='', array $attributes = []){
        // Value Attribute setzen
        $value = 'value="'.$value.'"';

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<input type="text" name="'.$name.'" class="form-control input-text '.$class.'" '.$value.' '.$attr.' >';
        return self::toHtmlString($ausgabe);
    }




    /**
     * Gibt ein verstecktes Input Feld zurück
     *
     * @param type $name
     * @param type $value
     * @param array $attributes
     * @return HTML
     */
    public static function hidden($name, $value = '', array $attributes = []){
        // Value Attribute setzen
        $value = 'value="'.$value.'"';

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<input type="hidden" name="'.$name.'" '.$value.' '.$attr.' />';
        return self::toHtmlString($ausgabe);
    }


    /**
     * Gibt ein Input Feld für Euro eingabe zurück,
     * mit 2 Nachkommastellen.
     *
     * @param type $name
     * @param float $value
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function euro($name='', $value='', $class='', array $attributes = []){
        // Value Attribute setzen
        if(is_numeric($value)){
            $value2 = 'value="'.  number_format($value, 2, ',', '.') .'"';
        }else{
            $value2 = 'value="'.  $value .'"';
        }


        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" id="'.$name.'" name="'.$name.'" class="form-control input-euro '.$class.'" aria-label="Euro" '.$value2.' '.$attr.' >';
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
     * @param type $name
     * @param float $value
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function prozent($name='', $value ='', $class='', array $attributes = []){
        // Value Attribute setzen
        if(is_numeric($value)){
            $value = 'value="'.  number_format($value, 0, ',', '.') .'"';
        }else{
            $value = 'value="'.  $value .'"';
        }

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);


        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" name="'.$name.'" class="form-control input-prozent '.$class.'" aria-label="Prozent" '.$value.' '.$attr.'>';
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
     * @param type $name
     * @param float $value
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function prozent1($name='', $value ='', $class='', array $attributes = []){
        // Value Attribute setzen
        if(is_numeric($value)){
            $value = 'value="'.  number_format($value, 1, ',', '.') .'"';
        }else{
            $value = 'value="'.  $value .'"';
        }

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);


        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" name="'.$name.'" class="form-control input-prozent '.$class.'" aria-label="Prozent" '.$value.' '.$attr.'>';
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
     * @param type $name
     * @param float $value
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function prozent2($name='', $value ='', $class='', array $attributes = []){
         // Value Attribute setzen
        if(is_numeric($value)){
            $value = 'value="'.  number_format($value, 2, ',', '.') .'"';
        }else{
            $value = 'value="'.  $value .'"';
        }

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);


        $ausgabe = '<div class="input-group">';
        $ausgabe .= '<input type="text" name="'.$name.'" class="form-control input-prozent '.$class.'" aria-label="Prozent" '.$value.' '.$attr.'>';
        $ausgabe .= '<div class="input-group-append">';
        $ausgabe .= '<span class="input-group-text">%</span>';
        $ausgabe .= '</div>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }




    /**
     * Gibt ein Input Feld für Integer eingabe zurück,
     *
     * @param type $name
     * @param type $value
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function int($name='', $value1 ='', $class='', array $attributes = []){
         // Value Attribute setzen
        if(is_numeric($value)){
            $value = 'value="'.  number_format($value, 0, ',', '.') .'"';
        }else{
            $value = 'value="'.  $value .'"';
        }

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<input type="text" name="'.$name.'" class="form-control input-int '.$class.'" '.$value.' '.$attr.'>';
        return self::toHtmlString($ausgabe);
    }





    /**
     * Gibt ein Input Feld für Zahlen eingabe zurück.
     *
     * @param type $name
     * @param type $value
     * @param int $nachkomma
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function number($name='', $value ='', $nachkomma = 0, $class='', array $attributes = []){
        // Value Attribute setzen
        if(is_numeric($value)){
            $value = 'value="'.  number_format($value, $nachkomma, ',', '.') .'"';
        }else{
            $value = 'value="'.  $value .'"';
        }

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<input type="text" name="'.$name.'" class="form-control input-zahl  '.$class.'" '.$value.' '.$attr.' >';
        return self::toHtmlString($ausgabe);
    }



    /**
     * Gibt ein Input Feld für Datum eingabe zurück.
     *
     * @param type $name
     * @param type $value
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function date($name='', $value ='', $class='', array $attributes = []){
        // Value Attribute setzen
        if($value != ''){
         $datum = new MyDateTime($value);
         $value = $datum->getDateDE();
        }
        $value = 'value="'.  $value .'"';

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);


        $ausgabe = '<input type="text" name="'.$name.'" class="form-control input-date '.$class.'" '.$value.' '.$attr.'" >';
        return self::toHtmlString($ausgabe);
    }




    /**
     * Gibt ein Input Feld für Passwort eingabe zurück.
     *
     * @param type $name
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function password($name='', $class='', array $attributes = []){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<input type="password" name="'.$name.'" class="form-control input-passw '.$class.'" '.$attr.' autocomplete="new-password">';
        return self::toHtmlString($ausgabe);
    }





    /**
     * Git eine TextArea zurück
     *
     * @param type $name
     * @param type $value
     * @param type $class
     * @param type $rows
     * @param array $attributes
     * @return type
     */
    public static function textArea($name='', $value ='', $class='', $rows=3, array $attributes = []){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);


        $ausgabe = '<textarea id="'.$name.'" name="'.$name.'" class="form-control '.$class.'" rows="'.$rows.'" '.$attr.'>'.$value.'</textarea>';
        return self::toHtmlString($ausgabe);
    }


    /**
     * Liefert ein Label zurück
     *
     * @param type $label
     * @param type $forID
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function label($label, $forID='', $class='', array $attributes = []){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<label class="form-check-label '.$class.'" for="'.$forID.'">'.$label.'</label>';
        return self::toHtmlString($ausgabe);
    }



    /**
     * Gibt eine CheckBox zurück
     *
     * @param type $name
     * @param type $label
     * @param type $value
     * @param type $checked
     * @param type $inline
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function checkBox($name='', $label='', $value ='', $checked=false, $inline=false, $class='', array $attributes = []){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        if($checked == false){
          $checked = '';
        }else{
          $checked = 'checked';
        }

        $ausgabe = '';
        if($inline == false){
          $ausgabe .= '<div class="form-check">';
        }else{
          $ausgabe .= '<div class="form-check form-check-inline">';
        }

        $ausgabe .= '<input type="checkbox" name="'.$name.'" class="form-check-input '.$class.'" value="'.$value.'" '.$checked.' '.$attr.'>';
        $ausgabe .= '<label class="form-check-label" for="'.$name.'">'.$label.'</label>';
        $ausgabe .= '</div>';

        return self::toHtmlString($ausgabe);
    }




    /**
     * Gibt eine RadioBox zurück
     *
     * @param type $id
     * @param type $group
     * @param type $label
     * @param type $value
     * @param type $checked
     * @param type $inline
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public  static function radioBox($id='', $group='', $label='', $value='', $checked=false, $inline=false, $class='', array $attributes = []){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        if($checked == false){
          $checked = '';
        }else{
          $checked = 'checked';
        }

        $ausgabe = '';
        if($inline == false){
          $ausgabe .= '<div class="form-check">';
        }else{
          $ausgabe .= '<div class="form-check form-check-inline">';
        }
        $ausgabe .= '<input type="radio" id="'.$id.'" name="'.$group.'" class="form-check-input '.$class.'" value="'.$value.'" '.$checked.' '.$attr.'>';
        $ausgabe .= '<label class="form-check-label" for="'.$id.'">'.$label.'</label>';
        $ausgabe .= '</div>';
        return self::toHtmlString($ausgabe);
    }



    /**
     * Gibt eine ComboBox zurück
     *
     * @param type $name
     * @param object $daten
     * @param type $selectedID
     * @param type $firstEntry
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function comboBox($name, $daten = null,$selectedID=0, $firstEntry='', $class='', array $attributes = []){
        // SellectedID ermitteln
        if($firstEntry == '' && $selectedID == 0){
            $selectedID = 1;
        }


        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '';

        $ausgabe = '<select name="'.$name.'" class="form-control '.$class.'" '. $attr . '>' . "\n";

        if($firstEntry != '' && $selectedID == 0){
           $ausgabe .= '<option value="0" selected>'.$firstEntry.'</option>' . "\n";
        }elseif($firstEntry != ''){
          $ausgabe .= '<option value="0">'.$firstEntry.'</option>' . "\n";
        }

        foreach ($daten as $row) {
            if($selectedID == $row->getFieldID()){
                $ausgabe .= '<option value="'.$row->getFieldID().'" selected>'.$row->getFieldBez(). '</option>' . "\n";
            }else{
                $ausgabe .= '<option value="'.$row->getFieldID().'">'.$row->getFieldBez(). '</option>' . "\n";
            }
        }

        $ausgabe .= '</select>' . "\n";
        return self::toHtmlString($ausgabe);
    }



    /**
     * Liefert eine ListBox zurück
     *
     * @param type $name
     * @param object $daten
     * @param type $selectedID
     * @param type $size
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function listBox($name, $daten = null, $selectedID=1, $size=5, $class='', array $attributes = [] ){

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<select multiple size="'.$size.'" name="'.$name.'" class="form-control '.$class.'" '.$attr.'>' . "\n";
        foreach ($daten as $row) {
          if ($selectedID == $row->getFieldID()){
            $ausgabe .= '<option value="'.$row->getFieldID().'" selected>'.$row->getFieldBez(). '</option>' . "\n";
          }else{
            $ausgabe .= '<option value="'.$row->getFieldID().'">'.$row->getFieldBez(). '</option>' . "\n";
          }
        }
        $ausgabe .= '</select>' . "\n";

        return self::toHtmlString($ausgabe);
    }



    /**
     * Gibt eine ComboBox zurück
     *
     * @param type $name
     * @param array $daten
     * @param type $selectedID
     * @param type $firstEntry
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function comboBoxArray($name, $daten = array(), $selectedID=0, $firstEntry='', $class='', array $attributes = []){
        // SellectedID ermitteln
        if($firstEntry == '' && $selectedID == 0){
            $selectedID = 1;
        }

        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<select id="'.$name.'" name="'.$name.'" class="form-control '.$class.'" '.$attr.'>' . "\n";

        if($firstEntry != '' && $selectedID == 0){
           $ausgabe .= '<option value="0" selected>'.$firstEntry.'</option>' . "\n";
        }elseif($firstEntry != ""){
          $ausgabe .= '<option value="0">'.$firstEntry.'</option>' . "\n";
        }

        foreach ($daten as $row) {
          if( $selectedID == $row[0]){
            $ausgabe .= '<option value="'.$row[0].'" selected>'.$row[1]. '</option>' . "\n";
          }else{
            $ausgabe .= '<option value="'.$row[0].'">'.$row[1]. '</option>' . "\n";
          }
        }
        $ausgabe .= '</select>' . "\n";
        return self::toHtmlString($ausgabe);
    }



    /**
     * Liefert eine ListBox zurück
     *
     * @param type $name
     * @param array $daten
     * @param type $selectedID
     * @param type $size
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function listBoxArray($name, $daten = array(), $selectedID=0, $size=5, $class='', array $attributes = [] ){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<select multiple size="'.$size.'" name="'.$name.'" class="form-control '.$class.'" '.$attr.'>' . "\n";
        foreach ($daten as $row) {
          if ($selectedID == $row[0]){
            $ausgabe .= '<option value="'.$row[0].'" selected>'.$row[1]. '</option>' . "\n";
          }else{
            $ausgabe .= '<option value="'.$row[0].'">'.$row[1]. '</option>' . "\n";
          }
        }
        $ausgabe .= '</select>' . "\n";
        return self::toHtmlString($ausgabe);
    }



    /**
     * Gibt eine Div die die Elemente Horizontal anordnet.
     *
     * @param type $class
     * @param array $attributes
     * @return type
     */
    public static function hGroupStart($class='mb-3', array $attributes = []){
        //Weitere Attribute setzen
        $attr = self::attributes($attributes);

        $ausgabe = '<div class="input-group '.$class.'" '.$attr.'>';
        return self::toHtmlString($ausgabe);
    }


    /**
     * Beendet die Horizontal Div
     *
     * @return type
     */
    public static function hGroupEnde(){
        return self::toHtmlString('</div>'."\n");
    }



    public static function com($comment){
        return self::toHtmlString('<!-- ' . $comment . ' -->');
    }

    public static function comStart(){
        return self::toHtmlString('<!-- ');
    }

    public static function comEnde(){
        return self::toHtmlString(' -->');
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


} //Class Ende

?>
