<?php
Namespace ITHilbert\LaravelKit\Classes\Bootstrap4;

/**
* Diese Klasse erstellt eine Card
* https://getbootstrap.com/docs/4.0/components/card/
* @version 1.0.0
**/

class Card{
  //Class Attribut
  public $class_card_group = 'card-group ';
  public $class_card_deck = 'card-deck ';


  public $class_card = "card bg-light mb-3 ";
  public $class_header = "card-header bold ";
  public $class_body = "card-body ";
  public $class_title = "card-title ";
  public $class_text = "card-text ";
  public $class_footer = "card-footer ";

  public $class_body_button_1 = "btn btn-primary ";
  public $class_body_button_2 = "btn btn-primary ";
  public $class_body_button_3 = "btn btn-primary ";

  public $class_footer_button_1 = "btn btn-primary ";
  public $class_footer_button_2 = "btn btn-primary ";
  public $class_footer_button_3 = "btn btn-primary ";

  public $class_card_button = "btn btn-primary ml-0";

  //Bilder
  public $header_img_src = '';        //Bild oben im Header
  public $header_img_alt = '';        //Bild oben im Header
  public $bg_img_src = '';              //Bild als Hintergrund
  public $bg_img_alt = '';              //Bild als Hintergrund
  public $footer_img_src = '';          //Bild unten
  public $footer_img_alt = '';          //Bild unten

  //Breites der Card
  public $width_card_max = '';

  //Inhalt (Haupt)
  public $header = "Header";
  public $body = "";
  public $titel = "";
  public $text = "";
  public $footer = "";

  //Buttons
  public $body_button_text_1 = "";
  public $body_button_text_2 = "";
  public $body_button_text_3 = "";
  public $body_button_link_1 = "";
  public $body_button_link_2 = "";
  public $body_button_link_3 = "";
  public $body_button_name_1 = "btnB1";
  public $body_button_name_2 = "btnB2";
  public $body_button_name_3 = "btnB3";
  public $body_button_value_1 = "true";
  public $body_button_value_2 = "true";
  public $body_button_value_3 = "true";
  public $body_button_type_1 = "link";    //link, button, submit, reset
  public $body_button_type_2 = "link";    //link, button, submit, reset
  public $body_button_type_3 = "link";    //link, button, submit, reset


  public $footer_button_text_1 = "";
  public $footer_button_text_2 = "";
  public $footer_button_text_3 = "";
  public $footer_button_link_1 = "#";
  public $footer_button_link_2 = "#";
  public $footer_button_link_3 = "#";
  public $footer_button_name_1 = "btnF1";
  public $footer_button_name_2 = "btnF2";
  public $footer_button_name_3 = "btnF3";
  public $footer_button_value_1 = "true";
  public $footer_button_value_2 = "true";
  public $footer_button_value_3 = "true";
  public $footer_button_type_1 = "link";    //link, button, submit, reset
  public $footer_button_type_2 = "link";    //link, button, submit, reset
  public $footer_button_type_3 = "link";    //link, button, submit, reset

  public $card_button_text = "";
  public $card_button_link = "#";
  public $card_button_name = "btnC1";
  public $card_button_value = "true";
  public $card_button_type = "submit";    //link, button, submit, reset


  /**
   * Text Zentrieren
   *
   * @return nothing
   * @public
   */
  public function setTextCenter(){
    $this->class_card .= ' text-center';
  }



  /**
   * Gibt eine Card zurück
   *
   * @return string card als HTML Element
   * @public
   */
  public function getCard(){
    //Header erstellen
    $ausgabe = $this->getCardHead();

    //Body
    $ausgabe .= $this->body;
    if ($this->titel != ""){
      $ausgabe .= '<h5 class="'.$this->class_title.'">'. $this->titel .'</h5>';
    }
    if ($this->text != ""){
      $ausgabe .= '<p class="'. $this->class_text .'">'. $this->text .'</p>';
    }

    //Body Buttons
    if ($this->body_button_text_1 != ""){
      if($this->body_button_type_1 == 'link'){
        $ausgabe .= '<a href="'. $this->body_button_link_1 .'" class="'. $this->class_body_button_1 .'" name="'. $this->body_button_name_1 .'">'. $this->body_button_text_1 .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->body_button_type_1 .'" class="'.$this->class_body_button_1.'" name="'. $this->body_button_name_1 .'" value="'. $this->body_button_value_1 .'">'. $this->body_button_text_1 .'</button>';
      }
    }
    if ($this->body_button_text_2 != ""){
      if($this->body_button_type_2 == 'link'){
        $ausgabe .= '<a href="'. $this->body_button_link_2 .'" class="'. $this->class_body_button_2 .'" name="'. $this->body_button_name_2 .'">'. $this->body_button_text_2 .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->body_button_type_2 .'" class="'.$this->class_body_button_2.'" name="'. $this->body_button_name_2 .'" value="'. $this->body_button_value_2 .'">'. $this->body_button_text_2 .'</button>';
      }
    }
    if ($this->body_button_text_3 != ""){
      if($this->body_button_type_3 == 'link'){
        $ausgabe .= '<a href="'. $this->body_button_link_3 .'" class="'. $this->class_body_button_3 .'" name="'. $this->body_button_name_3 .'">'. $this->body_button_text_3 .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->body_button_type_3 .'" class="'.$this->class_body_button_3.'" name="'. $this->body_button_name_3 .'" value="'. $this->body_button_value_3 .'">'. $this->body_button_text_3 .'</button>';
      }
    }

    //Footer
    $ausgabe .= $this->getCardFoot();

    return $ausgabe;
  }

  /**
   * Gibt den Head mit öffnen des Bodys einer Card zurück
   *
   * @return string card Head als HTML Element
   * @public
   */
  public function getCardHead($header = NULL, $class_header= NULL){
    if(!is_null($header)){
      $this->header = $header;
    }
    if(!is_null($class_header)){
      $this->class_header .= $class_header;
    }

    $ausgabe = "";
    if($this->width_card_max != ""){
      $ausgabe .= '<div class="'. $this->class_card .'" style="max-width: '. $this->width_card_max .';">';
    }else {
      $ausgabe .= '<div class="'. $this->class_card .'">';
    }
    //Header
    if($this->bg_img_src != ""){
      $ausgabe .= '<img class="card-img" src="'. $this->bg_img_src .'" alt="'. $this->bg_img_alt .'">';
      $ausgabe .= '<div class="card-img-overlay">';
    }
    if($this->header != ""){
      $ausgabe .= '<div class="'. $this->class_header .'">'. $this->header .'</div>';
    }
    if($this->header_img_src != ""){
        $ausgabe .= '<img class="card-img-top" src="'. $this->header_img_src .'" alt="'. $this->header_img_alt .'">';
    }
    //Body
    $ausgabe .= '<div class="'. $this->class_body .'">';

    return $ausgabe;
  }

  /**
   * Gibt den Foot einer Card zurück
   *
   * @return string card Foot als HTML Element
   * @public
   */
  public function getCardFoot($footer = NULL){
    if(!is_null($footer)){
      $this->footer = $footer;
    }

    $ausgabe = '';
    //Ende Body
    $ausgabe .= '</div>';
    //Footer
    //Footer
    if($this->footer != "" || $this->footer_button_text_1 != '' || $this->footer_button_text_2 != ''  || $this->footer_button_text_3 != ''){
      $ausgabe .= '<div class="'. $this->class_footer .'">';
    }

    if ($this->footer != ""){
      $ausgabe .= '<small class="text-muted">'. $this->footer .'</small>';
    }

    //Footer Buttons
    if ($this->footer_button_text_1 != ''){
      if($this->footer_button_type_1 == 'link'){
        $ausgabe .= '<a href="'. $this->footer_button_link_1 .'" class="'. $this->class_footer_button_1 .'" name="'. $this->footer_button_name_1 .'" value="'. $this->footer_button_value_1 .'">'. $this->footer_button_text_1 .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->footer_button_type_1 .'" class="'.$this->class_footer_button_1.'" name="'. $this->footer_button_name_1 .'" value="'. $this->footer_button_value_1 .'">'. $this->footer_button_text_1 .'</button>';
      }
    }
    if ($this->footer_button_text_2 != ''){
      if($this->footer_button_type_2 == 'link'){
        $ausgabe .= '<a href="'. $this->footer_button_link_2 .'" class="'. $this->class_footer_button_2 .'" name="'. $this->footer_button_name_2 .'" value="'. $this->footer_button_value_2 .'">'. $this->footer_button_text_2 .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->footer_button_type_2 .'" class="'.$this->class_footer_button_2.'" name="'. $this->footer_button_name_2 .'" value="'. $this->footer_button_value_2 .'">'. $this->footer_button_text_2 .'</button>';
      }
    }
    if ($this->footer_button_text_3 != ''){
      if($this->footer_button_type_3 == 'link'){
        $ausgabe .= '<a href="'. $this->footer_button_link_3 .'" class="'. $this->class_footer_button_3 .'" name="'. $this->footer_button_name_3 .'" value="'. $this->footer_button_value_3 .'">'. $this->footer_button_text_3 .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->footer_button_type_3 .'" class="'.$this->class_footer_button_3.'" name="'. $this->footer_button_name_3 .'" value="'. $this->footer_button_value_3 .'">'. $this->footer_button_text_3 .'</button>';
      }
    }

    //Footer schließen
    if($this->footer != "" || $this->footer_button_text_1 != '' || $this->footer_button_text_2 != ''  || $this->footer_button_text_3 != ''){
      $ausgabe .= '</div>';
    }

    //Hintergrundbild abschließen
    if($this->bg_img_src != ""){
      $ausgabe .= '</div>';
    }
    //Bild unten in der Card
    if($this->footer_img_src != ""){
        $ausgabe .= '<img class="card-img-bottom" src="'. $this->footer_img_src .'" alt="'. $this->footer_img_alt .'">';
    }

    //Card Button
    if ($this->card_button_text != ''){
      if($this->card_button_type == 'link'){
        $ausgabe .= '<a href="'. $this->card_button_link .'" class="'. $this->class_card_button .'" name="'. $this->card_button_name .'">'. $this->card_button_text .'</a>';
      }else{
        $ausgabe .= '<button type="'. $this->card_button_type .'" class="'.$this->class_card_button.'" name="'. $this->card_button_name .'" value="'. $this->card_button_value .'">'. $this->card_button_text .'</button>';
      }
    }

    //Card schließen
    $ausgabe .= '</div>';

    return $ausgabe;
  }

  //####### Gruppierungen ###############################################


  /**
   * Erstellt eine Card Group.
   *
   * @return string HTML Card Group
   * @public
   */
  public function getCardGroup(){
    return '<div class="'.$this->class_card_group.'">';
  }

  /**
   * Beendet die Card Group
   *
   * @return string HTML Card Group ende
   * @public
   */
  public function getCardGroupEnde(){
    return '</div>';
  }

  /**
   * Erstellt ein Card Deck
   *
   * @return string HTML Card Deck
   * @public
   */
  public function getCardDeck(){
    return '<div class="'.$this->class_card_deck.'">';
  }

  /**
   * Beendet ein Card Deck
   *
   * @return string HTML Card Deck ende
   * @public
   */
  public function getCardDeckEnde(){
    return '</div>';
  }

}
?>
