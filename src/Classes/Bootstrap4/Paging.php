<?php
Namespace ITHilbert\LaravelKit\Classes\Bootstrap4;

/**
* Diese Klasse erstellt eine Paging Element
* https://getbootstrap.com/docs/4.0/components/pagination/#disabled-and-active-states
* @version 1.0.0
**/

class Paging{

  /**
  *  Liefert ein Paging Element

  *  @param Integer $page Aktuelle Seite
  *  @param Integer $countPages Wie viele Seiten gibt es Insgesamt
  *  @param String  $comp Name der Componente für die Links
  *  @param String  $view Name der View für die Links
  *  @param Array   $filter Array mit den Filtereinstellungen.
  *  @param String  $sonstiges weitere Bestandteile der URL.
  *  @param Integer $maxPages Anzahl der Seiten, die dargestellt werden sollen

  * @const Integer LISTENTRIES Anzahl der Datensätze in einer Liste
  *  @const Integer MAXPAGES Anzahl der maximalen Pagingseiten
  */
  public function getPaging(int $page, int $countPages, $comp, $view, $filter, $sonstiges='', $maxPages = MAXPAGES){
    //Abbruch, wenn keine Seiten angezeigt werden ($countPages == 0) sollen, oder nur 1 Eintrag benötigt wird ($countPages == 1)
    if($countPages <= 1 ){
      return '';
    }

    //wenn filter übergeben wurden
    $strFilter = '';
    if(is_array($filter)){
        foreach ($filter as $key => $value) {
            $strFilter .= '&filter' . $key . '=' . $value;
        }
    }


    //Startpunkt ermitteln
    $start = $page - ceil($maxPages / 2);
    if(($start + ceil($maxPages / 2))>$countPages){$start = $countPages - $maxPages;}   //Kann Negativ werden
    if($start < 1){$start = 1;}

    //Ende
    $ende = $start + $maxPages;
    if($ende > $countPages){$ende = $countPages;}

    if($start == $ende){ return true;}


    echo '<nav aria-label="Navigation">';
    echo '<ul class="pagination justify-content-center">';  //justify-content-end -> rechtsbündig

    //Zurück zum Anfang
    if($start > 1){
      echo '<li class="page-item">';
      echo '<a class="page-link" href="?comp='.$comp.'&view='.$view.'&page=1'.$strFilter. $sonstiges . '" aria-label="First">';
      echo '<span aria-hidden="true" >&laquo;</span>';
      echo '<span class="sr-only">First</span>';
      echo '</a>';
      echo '</li>';
    }

    for($seite=$start; $seite<=$ende; $seite++){
      if($seite != $page){
        echo '<li class="page-item"><a class="page-link" href="?comp='.$comp.'&view='.$view.'&page='.$seite. $strFilter . $sonstiges. '">'.$seite.'</a></li>';
      }else{
        echo '<li class="page-item active">';
        echo '<a class="page-link" href="#">'.$seite.' <span class="sr-only">(current)</span></a>';
        echo '</li>';
      }
    }

    //Ans Ende springen
    if($ende < $countPages){
      echo '<li class="page-item">';
      echo '<a class="page-link" href="?comp='.$comp.'&view='.$view.'&page='.$countPages. $strFilter. $sonstiges.'" aria-label="Last">';
      echo '<span aria-hidden="true">&raquo;</span>';
      echo '<span class="sr-only">Last</span>';
      echo '</a>';
      echo '</li>';
    }

    echo '</ul>';
    echo '</nav>';
  }



}
?>
