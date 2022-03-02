<?php
Namespace ITHilbert\LaravelKit\App\Classes\Bootstrap4;

/**
* Diese Klasse erstellt ein Accordion - die Entsprechende JS Datei wird benötigt
* https://getbootstrap.com/docs/4.0/components/collapse/
* @version 1.0.0
**/
class Accordion{
  //Attribute
  public $id_accordion_group = "accordion";
  public $id_accordion = "1";
  public $body = "body";
  public $aria_expanded = 'true';
  public $button_text = "button_text";
  public $auswahl_link = true;

  //Class Attribut
  public $class_card = 'card ';
  public $class_card_header = 'card-header ';
  public $class_card_header_h5 = 'mb-0 ';
  public $class_button = 'btn btn-link ';
  public $class_div_content_show = 'collapse show ';
  public $class_div_content_collapsed = 'collapse collapsed ';
  public $class_body = 'card-body ';

  /**
  * __toString
  * @return string gibt den Inhalt von getAccordion zurück
  **/
  public function __toString(){
    return $this->getAccordion();
  }

  /**
  * Gibt ein Accordion Element zurück
  * @param $id       string  Die Id von diesem Element
  * @param $btnText  string  Der Text für den Block oben (Button)
  * @param $body     string  Der Inhalt von dem Element
  * @param $expanded string  true/false ob das Element aufgeklappt oder geschlossen ist
  * @return string  Das Element als HTML Code
  **/
  public function getAccordion($id= '', $btnText = '' , $body= '', $expanded = '', $textright = ''){
    //wenn keine Parameter ügerbenen wurden, dann nutze die der Attribute
    if($id == ''){
      $id = $this->id_accordion;
    }

    if($btnText == ''){
      $btnText = $this->button_text;
    }

    if($body == ''){
      $body = $this->body;
    }

    if($expanded == ''){
      $expanded = $this->aria_expanded;
    }

    //Ausgabe erstellen
    $ausgabe = '';
    $ausgabe .= '<div class="'.$this->class_card.'">'."\n";
    $ausgabe .= '<div class="'.$this->class_card_header.'" id="heading'. $id .'">'."\n";
    $ausgabe .= '<div class="row">';
    $ausgabe .= '<div class="col" style="margin-left: -20px;">';
    if($this->auswahl_link){
      $ausgabe .= '    <a class="'.$this->class_button.'" data-toggle="collapse" href="#collapse'.$id.'" role="button" aria-expanded="false" aria-controls="collapse'.$id.'">';
      $ausgabe .=       $btnText;
      $ausgabe .= '    </a>'."\n";
    }else{
      $ausgabe .= '    <button class="'.$this->class_button.'" data-toggle="collapse" data-target="#collapse'.$id.'" aria-expanded="'.$expanded.'" aria-controls="collapse'.$id.'">';
      $ausgabe .=       $btnText;
      $ausgabe .= '    </button>'."\n";
    }
    $ausgabe .= '</div>'."\n";
    $ausgabe .= '<div class="col text-right pt-2">'.$textright.'</div>'."\n";
    $ausgabe .= '</div>'."\n";
    $ausgabe .= '</div>'."\n";

    if($this->aria_expanded== 'true'){
      $ausgabe .= '<div id="collapse'.$id.'" class="'.$this->class_div_content_show.'" aria-labelledby="heading'.$id.'" data-parent="#'.$this->id_accordion_group.'">'."\n";
    }else{
      $ausgabe .= '<div id="collapse'.$id.'" class="'.$this->class_div_content_collapsed.'" aria-labelledby="heading'.$id.'" data-parent="#'.$this->id_accordion_group.'">'."\n";
    }
    $ausgabe .= '  <div class="'.$this->class_body.'">'."\n";
    $ausgabe .= $body;
    $ausgabe .= '  </div>'."\n";
    $ausgabe .= '</div>'."\n";
    $ausgabe .= '</div>'."\n";

    return $ausgabe;
  }

  /**
  * Gibt eine Accordion Gruppe zurück
  * @param $id  $string Die ID/Name der Gruppe
  * @return string  Das Grouppenelement als HTML Code
  **/
  public function getAccordionGroup($id = ''){
    if($id == ''){
      $id = $this->id_accordion_group;
    }

    if($id != $this->id_accordion_group){
      $this->id_accordion_group = $id;
    }

    return '<div id="'.$id.'">'."\n";
  }


  /**
  * Gibt eine Accordion Gruppen Ende zurück
  * @return string  Das Grouppenelement Ende als HTML Code
  **/
  public function getAccordionGroupEnde(){
    return '</div>'."\n";
  }


}
 ?>
