<?php
Namespace ITHilbert\LaravelKit\App\Classes\Bootstrap4;

/**
* Diese Klasse erstellt einen Button
* https://getbootstrap.com/docs/4.0/components/buttons/
* @version 1.0.0
**/

class Button{

  //Attribute Button
  public $button_type = 'submit';                 //button, submit, reset
  public $button_class = 'btn bg-primary ';
  public $button_name = 'btn';
  public $button_value = 'true';
  public $button_text = 'Button';
  public $button_style = '';

  //Attribute Link Button
  public $link_text = 'link';
  public $link_name = 'Link';
  public $link_link = '';
  public $link_class = 'btn bg-primary';
  public $link_style = '#';

  public function getButton($text = NULL, $name = NULL, $type = NULL, $value =NULL, $class = NULL, $style = NULL){
    if(is_null($text)){
      $text = $this->button_text;
    }
    if(is_null($name)){
      $name = $this->button_name;
    }
    if(is_null($type)){
      $type = $this->button_type;
    }
    if(is_null($value)){
      $value = $this->button_value;
    }
    if(is_null($class)){
      $class = $this->button_class;
    }
    if(is_null($style)){
      $style = $this->button_style;
    }

    $ausgabe = '<button type="'. $type .'" class="'.$class.'" name="'. $name .'" value="'. $value .'" style="'. $style .'">'. $text .'</button>';
    return $ausgabe;
  }


  public function getLinkButton($text = NULL, $link = NULL, $name = NULL, $class = NULL, $style = NULL){
    if(is_null($text)){
      $text = $this->link_text;
    }
    if(is_null($link)){
      $link = $this->link_link;
    }
    if(is_null($name)){
      $name = $this->link_name;
    }
    if(is_null($class)){
      $class = $this->link_class;
    }
    if(is_null($style)){
      $style = $this->link_style;
    }

    $ausgabe = '<a href="'. $link .'" class="'. $class .'" role="button"  name="'. $name .'" style="'. $style .'">'. $text .'</a>';

    return $ausgabe;
  }


  /**
   * Undocumented function
   *
   * @param string $text Bezeichnung vom Button
   * @param string $comp Name der Componente
   * @param string $view  Name der View
   * @param string $value weitere Infos wie Btn Name und Values
   * @param string $class weiteres classen
   * @param string $style Neue Styles
   * @return void
   */
  public function getBack($text = 'Zurück', $comp, $view, $value='', $class='', $style=''){
    $ausgabe = '<a href="?comp='.$comp.'&view='.$view. $value.'" style="'.$style.'" class="btn bg-warning text-black '.$class.'">'. $text .'</a>';
    return $ausgabe;
  }



  public function getAdd($comp, $view, $value=true, $class='', $style='color:white;'){
    $ausgabe = '<a href="?comp='.$comp.'&view='.$view.'&btn-add='.$value.'" style="'.$style.'" class="material-icons '.$class.'">add_circle_outline</a>';
    return $ausgabe;
  }

  public function getEdit($comp, $view, $id, $class='editButton', $style='color:blue;'){
    $ausgabe = '<a href="?comp='.$comp.'&view='.$view.'&btn-edit='. $id .'" style="'.$style.'" class="material-icons '.$class.'" title="bearbeiten">edit</a>';
    return $ausgabe;
  }

  public function getSort($comp, $view, $id, $class='sortButton', $style='color:#04B404;'){
    $ausgabe = '<a href="?comp='.$comp.'&view='.$view.'&btn-edit='. $id .'" style="'.$style.'" class="material-icons '.$class.'" title="sortieren" >storage</a>';
    return $ausgabe;
  }

  //Ein Button der eine Liste von Aufträgen öffnet für einen Arzt
  public function getAuftragFilter($filter, $class='auftragButton', $style='color:#04B404;'){
    $ausgabe = '<a href="?comp=auftraege&view=auftraege.list&'. $filter .'" style="'.$style.'" class="material-icons '.$class.'" title="Auftr&auml;ge" >assignment</a>';
    return $ausgabe;
  }


  public function getDelete($comp, $view, $id, $class='removeButton', $style='color:red;'){
    $ausgabe = '<a href="#" id="'. $id .'" style="'.$style.'" class="material-icons '.$class.'" title="löschen">remove_circle</a>';
    return $ausgabe;
  }

//  public function getDelete($comp, $view, $id, $class='removeButton', $style='color:red;'){
//    $ausgabe = '<a href="?comp='.$comp.'&view='.$view.'&btn-remove='. $id .'" style="'.$style.'" class="material-icons '.$class.'">remove_circle</a>';
//    return $ausgabe;
//  }


  public function getSelect($comp, $view, $id, $class='selectButton', $style='color:black;'){
    $ausgabe = '<a href="?comp='.$comp.'&view='.$view.'&btn-select='. $id .'" style="'.$style.'" class="material-icons '.$class.'" title="Auswählen">check</a>';
    return $ausgabe;
  }

  public function getSave($text='Speichern' , $class='', $style=''){
    if($style != ''){
      $style = 'style="' . $style . '"';
    }
    $ausgabe = '<button type="submit" class="btn bg-primary '.$class.'" name="btn-save" value="true" '.$style.'>'.$text.'</button>';
    return $ausgabe;
  }




  public function getAddButton($name = 'btn-add'){
    $ausgabe = '<i id="'.$name.'" name="'.$name.'" class="material-icons btn-add" style="color:green;">add_circle_outline</i>';
    return $ausgabe;
  }

  public function getEditButton($id, $name = 'btn-edit'){
    $ausgabe = '<i id="'.$name.'" name="'.$name.'" value="'.$id.'" class="material-icons btn-edit" style="color:red;">edit</i>';
    return $ausgabe;
  }

  public function getDeleteButton($id, $name = 'btn-remove'){
    $ausgabe = '<i id="'.$name.'" name="'.$name.'" value="'.$id.'" class="material-icons btn-remove" style="color:red;">remove_circle</i>';
    return $ausgabe;
  }

  public function getSelectButton($id, $name = 'btn-select'){
    $ausgabe = '<i id="'.$name.'" name="'.$name.'" value="'.$id.'" class="material-icons btn-select" style="color:black;">check_square</i>';
    return $ausgabe;
  }


}
?>
