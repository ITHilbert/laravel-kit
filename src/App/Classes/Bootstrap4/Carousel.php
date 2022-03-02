<?php
Namespace ITHilbert\LaravelKit\App\Classes\Bootstrap4;

/**
* Diese Klasse erstellt ein Image Carousel - die Entsprechende JS Datei wird benötigt
* http://getbootstrap.com/docs/4.1/components/carousel/
* @version 1.0.0
**/
class Carousel{
  //Class Attribitte
  public $class_carousel            = 'carousel slide ';
  public $class_indicators          = 'carousel-indicators ';
  public $class_inner               = 'carousel-inner ';
  public $class_item_active         = 'carousel-item active ';
  public $class_item                = 'carousel-item ';
  public $class_img                 = 'd-block w-100 ';
  public $class_img_caption         = 'carousel-caption d-none d-md-block ';
  public $class_control_prev        = 'carousel-control-prev ';
  public $class_control_next        = 'carousel-control-next ';

  public $class_control_prev_icon   = 'carousel-control-prev-icon ';
  public $class_control_next_icon   = 'carousel-control-next-icon ';


  //Attribute
  public $id_carousel = 'carouselExampleIndicators';
  protected $sec = array();
  protected $alt = array();
  protected $title = array();
  protected $text = array();


  /**
  * Ein Bild hinzufügen
  * @param $src string  Pfad zum Bild
  * @param $alt string  Text für das alt Attribut
  * @return $this
  **/
  public function addImage($src, $alt = '', $title='', $text=''){
    $this->src[] = $src;
    $this->alt[] = $alt;
    $this->title[] = $title;
    $this->text[] = $text;
    return $this;
  }


  public function getCarousel(){
    $anz = count($this->src);

    $ausgabe = '';

    $ausgabe .= '<div id="'.$this->id_carousel.'" class="'.$this->class_carousel.'" data-ride="carousel">';
    $ausgabe .= '<ol class="'.$this->class_indicators.'">';

    //Erster Eintrag
    $ausgabe .= '<li data-target="#'.$this->id_carousel.'" data-slide-to="0" class="active"></li>';
    //Alle weiteren Einträge
    for($i=1; $i<$anz; $i++){
      $ausgabe .= '<li data-target="#'.$this->id_carousel.'" data-slide-to="'.$i.'"></li>';
    }

    $ausgabe .= '</ol>';
    $ausgabe .= '<div class="'.$this->class_inner.'">';

    //Erster Eintrag
    $ausgabe .= '<div class="'.$this->class_item_active.'">';
    $ausgabe .= '<img class="'.$this->class_img.'" src="'.$this->src[0].'" alt="'.$this->alt[0].'">';    //&text=First slide

    $ausgabe .= '<div class="'.$this->class_img_caption.'">';
    $ausgabe .= '<h5>'.$this->title[0].'</h5>';
    $ausgabe .= '<p>'.$this->text[0].'</p>';
    $ausgabe .= '</div>';

    $ausgabe .= '</div>';
    //Alle weiteren Einträge
    for($i=1; $i<$anz; $i++){
      $ausgabe .= '<div class="'.$this->class_item.'">';
      $ausgabe .= '<img class="'.$this->class_img.'" src="'.$this->src[$i].'" alt="'.$this->alt[$i].'">'; //?auto=yes&bg=666&fg=444&text=Second slide

      $ausgabe .= '<div class="'.$this->class_img_caption.'">';
      $ausgabe .= '<h5>'.$this->title[$i].'</h5>';
      $ausgabe .= '<p>'.$this->text[$i].'</p>';
      $ausgabe .= '</div>';

      $ausgabe .= '</div>';
    }
    $ausgabe .= '</div>';
    //Vor- & Zurück Navigation
    $ausgabe .= '<a class="'.$this->class_control_prev.'" href="#'.$this->id_carousel.'" role="button" data-slide="prev">';
    $ausgabe .= '<span class="'.$this->class_control_prev_icon.'" aria-hidden="true"></span>';
    $ausgabe .= '<span class="sr-only">Previous</span>';
    $ausgabe .= '</a>';
    $ausgabe .= '<a class="'.$this->class_control_next.'" href="#'.$this->id_carousel.'" role="button" data-slide="next">';
    $ausgabe .= '<span class="'.$this->class_control_next_icon.'" aria-hidden="true"></span>';
    $ausgabe .= '<span class="sr-only">Next</span>';
    $ausgabe .= '</a>';
    $ausgabe .= '</div>';

    return $ausgabe;
  }



}
?>
