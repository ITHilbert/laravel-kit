<?php

namespace ITHilbert\LaravelKit\Traits;

use ITHilbert\LaravelKit\Entities\ComboBox;

/*
 * Some functions for the vue combobox
 */

trait VueComboBox
{
    //Das führ dazu, dass die funktionen getCbKeyAttribute und getCbCaptionAttribute erstellt werden müssen.
    //Dadurch stehen die Attribute cbKey und cbCaption zur Verfgung.
    //
    //Eloquent ruft initialize{TraitName}() automatisch im Model-Konstruktor auf,
    //NACHDEM die Attribute gesetzt wurden. Ein eigener __construct() würde
    //parent::__construct() ohne $attributes aufrufen und damit Massenzuweisung /
    //Factory-Erstellung verschlucken ("Field 'email' doesn't have a default value").
    public function initializeVueComboBox(): void
    {
        $this->appends[] = 'cbKey';
        $this->appends[] = 'cbCaption';
    }


    public function getCbKeyAttribute(){
        return $this->id;
    }

    public function getCbCaptionAttribute(){
        return $this->caption;
    }

    public static function getComboBoxData(){
        $options = parent::all();


        $liste = array();
        $anz = 0;
        foreach($options as $option){
            $liste[$anz] = new ComboBox();
            $liste[$anz]->cbKey = $option->cbKey;
            $liste[$anz]->cbCaption = $option->cbCaption;
            $anz++;
        }

        return json_encode($liste);
    }

}
