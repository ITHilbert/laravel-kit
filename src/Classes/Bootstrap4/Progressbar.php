<?php
Namespace ITHilbert\LaravelKit\Classes\Bootstrap4;

/**
* Diese Klasse erstellt eine Progressbar Element
* https://getbootstrap.com/docs/4.0/components/progress/#animated-stripes
* @version 1.0.0
**/

class Progressbar{

  /**
  *  Progressbar - Bootstrap 4
  */
  public function progressbar(){
    echo '<div class="progress">';
    echo '  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 75%">O %</div>';
    echo '</div>';
  }


}
?>
