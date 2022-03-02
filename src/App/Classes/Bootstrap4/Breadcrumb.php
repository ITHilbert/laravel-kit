<?php
Namespace ITHilbert\LaravelKit\App\Classes\Bootstrap4;

/**
*  Erstellt einen Breadcrumb
*  https://getbootstrap.com/docs/4.0/components/breadcrumb/
*  @version 1.0.0
**/

class Breadcrumb{

  public $class_ol = 'breadcrumb ';
  public $class_li = 'breadcrumb-item ';
  public $class_li_aktive = 'breadcrumb-item active ';
  public $aria_label= 'breadcrumb';


  private $page = Array();
  private $link = Array();

  /**
 *  Fügt einen Eintrag zum Breadcrumb hinzu
 *  @param string $pageName Name der Seite
 *  @param string $linkValue URI zur Seite
 *  @return this
 *  @public
   */
  public function addEntrie($pageName, $linkValue){
    $this->page[] = $pageName;
    $this->link[] = $linkValue;
    return $this;
  }

  /**
   * Gibt den Breadcrumb zurück
   *
   * @return string HTML Breadcrumb
   * @public
   */
  public function getBreadcrumb(){
    $ausgabe = '<nav aria-label="'.$this->aria_label.'">';
    $ausgabe .= '<ol class="'.$this->class_ol.'">';

    $anz = count($this->page) - 1;

    foreach ($this->page as $key => $value) {
      if($key < $anz){
        $ausgabe .= '<li class="'. $this->class_li .'"><a href="'. $this->link[$key] .'">'. $value .'</a></li>';
      }else{
        $ausgabe .= '<li class="'. $this->class_li_aktive .'" aria-current="page">'. $value .'</li>';
      }
    }

    $ausgabe .= '</ol>';
    $ausgabe .= '</nav>';

    return $ausgabe;
  }
}
?>
