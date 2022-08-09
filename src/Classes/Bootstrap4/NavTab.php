<?php
Namespace ITHilbert\LaravelKit\Classes\Bootstrap4;

/**
* Diese Klasse erstellt eine Navigation mit Reitern
* https://getbootstrap.com/docs/4.0/components/navs/
* @version 1.0.0
**/

class NavTab{

  public $tabID = 'myTab';
  public $tabs = array();     //Array mit den NavtabItems

  public function addTab($tabID, $tabText, $body){
    $this->tabs[] = new NavtabItem($tabID, $tabText, $body);
  }

  public function __toString(){
    return $this->getTab();
  }

  public function getTab(){
    $anz=0;
    $ausgabe = '';
    $ausgabe .= '<ul class="nav nav-tabs" id="'.$this->tabID.'" role="tablist">'."\n";
    foreach ($this->tabs as $item) {
      $ausgabe .= '<li class="nav-item">'."\n";
      if($anz == 0){
        $ausgabe .= '<a class="nav-link active" id="'.$item->tabID.'-tab" data-toggle="tab" href="#'.$item->tabID.'" role="tab" aria-controls="'.$item->tabID.'" aria-selected="true">'.$item->tabText.'</a>'."\n";
      }else{
        $ausgabe .= '<a class="nav-link" id="'.$item->tabID.'-tab" data-toggle="tab" href="#'.$item->tabID.'" role="tab" aria-controls="'.$item->tabID.'" aria-selected="false">'.$item->tabText.'</a>'."\n";
      }
      $ausgabe .= '</li>'."\n";
      $anz++;
    }
    $ausgabe .= '</ul>'."\n";
    $ausgabe .= '<div class="tab-content" id="'.$this->tabID.'Content">'."\n";

    $anz =0;
    foreach ($this->tabs as $item) {
      if($anz == 0){
        $ausgabe .= '<div class="tab-pane fade show active" id="'.$item->tabID.'" role="tabpanel" aria-labelledby="'.$item->tabID.'-tab">'.$item->body.'</div>'."\n";
      }else{
        $ausgabe .= '<div class="tab-pane fade" id="'.$item->tabID.'" role="tabpanel" aria-labelledby="'.$item->tabID.'-tab">'.$item->body.'</div>'."\n";
      }
    $anz++;
    }
    $ausgabe .= '</div>'."\n";

    return $ausgabe;
  }







}

class NavtabItem{
  public $tabText;
  public $tabID;
  public $body;

  public function __construct($tabID, $tabText, $body){
    $this->tabID = $tabID;
    $this->tabText = $tabText;
    $this->body = $body;
  }

}



?>
