<?php
Namespace ITHilbert\LaravelKit\App\Classes\Bootstrap4;

/**
* Diese Klasse erstellt Textboxen
* https://getbootstrap.com/docs/4.0/components/input-group/
*
* @version 1.0.0
**/
class Input{
  //Class Attribitte
  private $class = 'mb-3';
  private $readonly = '';
  private $autocomplete = 'autocomplete="off"';

  public function setClass($class = 'mb-3'){
    $this->class = $class;
    return $this;
  }

  public function getClass(){
    return $this->class;
  }

  public function setReadonlyOn(){
    $this->readonly = 'readonly';
    return $this;
  }

  public function setReadonlyOff(){
    $this->readonly = '';
    return $this;
  }

  public function getReadonly(){
    return $this->readonly;
  }


  public function setAutocompleteOn(){
    $this->autocomplete = 'autocomplete="on"';
    return $this;
  }

  public function setAutocompleteOff(){
    $this->autocomplete = 'autocomplete="off"';
    return $this;
  }

  public function getAutocomplete(){
    return $this->autocomplete;
  }




  /**
  Liefert ein Hidden Feld zurück
  @param $name string Name Attribut
  @param $value string Value Attribut
  @return string Hidden Input Feld als HTML String
  **/
  public function getHidden($name, $value){
    return '<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.$value.'" />';
  }

  /**
  * Liefert ein Hidden Feld zurück,
  * wo das Attribut Name = comp ist.
  * @param $value string Value Attribut
  * @return string Hidden Input Feld als HTML String
  **/
  public function getHComp($value){
    return $this->getHidden('comp', $value);
  }

  /**
  * Liefert ein Hidden Feld zurück,
  * wo das Attribut Name = view ist.
  * @param $value string Value Attribut
  * @return string Hidden Input Feld als HTML String
  **/
  public function getHView($value){
    return $this->getHidden('view', $value);
  }

  public function getInputEuro($name='', $value ='', $placeholder='',$decimal=2, $class='', $style='', $attr=''){
    if($value!=''){
        $value = 'value="'.  number_format($value, $decimal, ',', '.') .'"';
    }
    $ausgabe = '<div class="input-group '.$this->class.'">';
    $ausgabe .= '<input type="text" id="'.$name.'" name="'.$name.'" class="form-control input-euro '.$class.'" aria-label="Euro" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '. $this->autocomplete .' '.$attr.' >';
    $ausgabe .= '<div class="input-group-append">';
    $ausgabe .= '<span class="input-group-text"> &euro; </span>';
    $ausgabe .= '</div>';
    $ausgabe .= '</div>';
    return $ausgabe;
  }

  public function getInputProzent($name='', $value ='', $placeholder='',$decimal=0, $class='', $style='', $attr=''){
    if($value!=''){
        $value = 'value="'.  number_format($value, $decimal, ',', '.') .'"';
    }
    $ausgabe = '<div class="input-group '.$this->class.'">';
    $ausgabe .= '<input type="text" id="'.$name.'" name="'.$name.'" class="form-control input-prozent '.$class.'" aria-label="Prozent" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.' '.$attr.'>';
    $ausgabe .= '<div class="input-group-append">';
    $ausgabe .= '<span class="input-group-text">%</span>';
    $ausgabe .= '</div>';
    $ausgabe .= '</div>';
    return $ausgabe;
  }

  public function getInputInt($name='', $value ='', $placeholder='', $class='', $style='', $attr=''){
    if($value!=''){
        $value = 'value="'.  number_format($value, 0, ',', '.') .'"';
    }
    $ausgabe = '<input type="text" id="'.$name.'" name="'.$name.'" class="form-control input-int '.$class.'" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.' '.$attr.'>';
    return $ausgabe;
  }

  public function getInputZahl($id='', $name='', $value ='', $placeholder='', $class='', $style='', $attr=''){
    if($value!=''){
        //Prüfen ob Nachkommastellen vorhanden
        $tempzahl = explode(".", $value);
        if (isset($tempzahl[1])) {
          $value = 'value="'.  number_format($value, 2, ',', '.') .'"';
        }else{
          $value = 'value="'.  number_format($value, 0, ',', '.') .'"';
        }
    }
    $ausgabe = '<input type="text" id="'.$id.'" name="'.$name.'" class="form-control input-zahl  '.$class.'" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.' '.$attr.' >';
    return $ausgabe;
  }


  public function getInputText($name='', $value ='', $placeholder='', $class='', $style='', $attr=''){
    if($value!=''){
        $value = 'value="'. $value .'"';
    }
    $ausgabe = '<input type="text" id="'.$name.'" name="'.$name.'" class="form-control input-text '.$class.'" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.' '.$attr.' >';
    return $ausgabe;
  }

  public function getInputText2($name='', $value ='', $placeholder='', $class='', $style='', $attr=''){
    if($value!=''){
        $value = 'value="'. $value .'"';
    }
    $ausgabe = '<input type="text" id="'.$name.'" name="'.$name.'" class="input-text border-0 '.$class.'" '.$value.' placeholder="'.$placeholder.'" readonly '.$this->autocomplete.' style="background-color: transparent;" '.$attr.'>';
    return $ausgabe;
  }


  public function getInputDate($name='', $value ='', $placeholder='', $class=''){
    if($value!=''){
        $value = 'value="'. $value .'"';
    }
    $ausgabe = '<input type="text" id="'.$name.'" name="'.$name.'" class="form-control input-date '.$class.'" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.' pattern="^(31|30|0[1-9]|[12][0-9]|[1-9])\.(0[1-9]|1[012]|[1-9])\.((18|19|20)\d{2}|\d{2})$">';
    return $ausgabe;
  }


  public function getInputPassword($name='', $value ='', $placeholder='Passwort'){
    if($value!=''){
        $value = 'value="'. $value .'"';
    }
    $ausgabe = '<input type="password" id="'.$name.'" name="'.$name.'" class="form-control input-text2" '.$value.' placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.'>';
    return $ausgabe;
  }

  public function getTextArea($name='', $value ='', $placeholder='', $rows=3){
    $ausgabe = '<textarea id="'.$name.'" name="'.$name.'" class="form-control" rows="3"  placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.'>'.$value.'</textarea>';
    return $ausgabe;
  }

  /**
   * Liefer eine Input Box mit Attribut List
   *
   * @param string $name
   * @param string $value
   * @param string $datalist
   * @param string $placeholder
   * @param string $class
   * @param string $style
   * @param string $attr
   * @return void
   */
  public function getInputDataList($name='', $value ='', $datalist='', $placeholder='', $class='', $style='', $attr=''){
    $ausgabe = '<input type="text" id="'.$name.'" name="'.$name.'" list="'.$datalist.'" class="form-control input-datalist '.$class.'" value="'.$value.'" placeholder="'.$placeholder.'" '.$this->readonly.' '.$this->autocomplete.' '.$attr.'>';

    return $ausgabe;
  }


  /**
   * Erstellt eine Checkbox
   *
   * @param string $name
   * @param string $label
   * @param boolean $value
   * @param boolean $checked
   * @param boolean $inline
   * @param string $tag
   * @return void
   */
  public function getCheckBox($name='', $label='', $value=true, $checked=false, $inline=false, $tag=''){
    if($checked == false){
      $checked = '';
    }else{
      $checked = 'checked';
    }

    $dis = '';
    if($this->readonly == true){
        $dis = 'disabled';
    }

    $ausgabe = '';
    if($inline == false){
      $ausgabe .= '<div class="form-check">';
    }else{
      $ausgabe .= '<div class="form-check form-check-inline">';
    }
    $ausgabe .= '<input type="checkbox" id="'.$name.'" name="'.$name.'" class="form-check-input" value="'.$value.'" '. $tag .' '.$dis.' '. $checked .'>';
    if($label != ''){
      $ausgabe .= '<label class="form-check-label" for="'.$name.'">'.$label.'</label>';
    }
    $ausgabe .= '</div>';
    return $ausgabe;
  }

  public function getLabelCheckBox($name='', $label= '', $value=true, $checked=false, $class='', $inline=false, $tag=''){
    if($checked == false){
      $checked = '';
    }else{
      $checked = 'checked';
    }

    $dis = '';
    if($this->readonly == true){
        $dis = 'disabled';
    }

    $ausgabe = '';
    if($inline == false){
      $ausgabe .= '<div class="form-check">';
    }else{
      $ausgabe .= '<div class="form-check form-check-inline">';
    }
    if($label != ''){
      $ausgabe .= '<label class="form-check-label" for="'.$name.'">'.$label.'</label>';
    }
    $ausgabe .= '<input type="checkbox" id="'.$name.'" name="'.$name.'" class="form-check-input" value="'.$value.'" '. $tag .' '.$dis.' '. $checked .'>';
    $ausgabe .= '</div>';
    return $ausgabe;
  }


  public function getCheckBoxOnly($name='', $checked=false, $value=true, $class=''){
    if($checked == false){
      $checked = '';
    }else{
      $checked = 'checked';
    }

    $dis = '';
    if($this->readonly == true){
        $dis = 'disabled';
    }

    $ausgabe = '';

    $ausgabe .= '<input type="checkbox" id="'.$name.'" name="'.$name.'" class="form-check-input '.$class.'" value="'.$value.'" '.$dis.' '. $checked .'>';

    return $ausgabe;
  }



  public function getRadioBox($id='', $group='', $label='', $value='', $checked=false, $inline=false, $tag=''){
    if($checked == false){
      $checked = '';
    }else{
      $checked = 'checked';
    }

    $dis = '';
    if($this->readonly == true){
        $dis = 'disabled';
    }

    if($tag != ''){
      $tag = 'tag="' . $tag . '"';
    }

    $ausgabe = '';
    if($inline == false){
      $ausgabe .= '<div class="form-check">';
    }else{
      $ausgabe .= '<div class="form-check form-check-inline">';
    }
    $ausgabe .= '<input type="radio" id="'.$id.'" name="'.$group.'" class="form-check-input" value="'.$value.'" '.$tag.' '.$dis.' '. $checked .'>';
    if($label != ''){
      $ausgabe .= '<label class="form-check-label" for="'.$id.'">'.$label.'</label>';
    }
    $ausgabe .= '</div>';
    return $ausgabe;
  }


  public function getLabel($label, $forID=''){
    $ausgabe = '<label class="form-check-label" for="'.$forID.'">'.$label.'</label>';
    return $ausgabe;
  }


  /**
  * @param $name string Für das Attribut name und ID
  * @param $daten array Inhalt der Combobox. [0] = ID und [1]=Value
  * @param $id  int   Die ID für den Select
  * @param $FirstEntry string  Erster Eintrag mit dem Wert 0, der nicht aus den Daten kommt (z.B. Alle, auswahl, kein Filter usw.)
  * @param $class string Für das Klassen Attribut
  * @param $style string Für das Style Attribut
  * @return string  Eine Combobox im HTML Format
  **/
  public function getComboBox($name, $daten = array(), $id=1, $firstEntry='', $class='', $style=''){
    if($class == ''){
        $class = $this->class;
    }

    $dis = '';
    if($this->readonly == true){
        $dis = 'disabled';
    }


    $ausgabe = '<select id="'.$name.'" name="'.$name.'" class="form-control '.$class.'" '.$dis.' style="'.$style.'">' . "\n";

    if($firstEntry != '' && $id == 0){
       $ausgabe .= '<option value="0" selected>'.$firstEntry.'</option>' . "\n";
    }elseif($firstEntry != ""){
      $ausgabe .= '<option value="0">'.$firstEntry.'</option>' . "\n";
    }

    foreach ($daten as $row) {
      if ($id == $row[0]){
        $ausgabe .= '<option value="'.$row[0].'" selected>'.$row[1]. '</option>' . "\n";
      }else{
        $ausgabe .= '<option value="'.$row[0].'">'.$row[1]. '</option>' . "\n";
      }
    }
    $ausgabe .= '</select>' . "\n";
    //$ausgabe .= '</div>';
    return $ausgabe;
  }

  /**
  * @param $name string Für das Attribut name und ID
  * @param $daten array Inhalt der Listbox. [0] = ID und [1]=Value
  * @param $id  int   Die ID für den Select
  * @param $size int Anzahl der Zeilen
  * @return string  Eine Listbox im HTML Format
  **/
  public function getListBox($name, $daten = array(), $id=1, $size=5 ){
    $ausgabe = '<select multiple size="'.$size.'" id="'.$name.'" name="'.$name.'" class="form-control" '.$this->readonly.'>' . "\n";
    foreach ($daten as $row) {
      if ($id == $row[0]){
        $ausgabe .= '<option value="'.$row[0].'" selected>'.$row[1]. '</option>' . "\n";
      }else{
        $ausgabe .= '<option value="'.$row[0].'">'.$row[1]. '</option>' . "\n";
      }
    }
    $ausgabe .= '</select>' . "\n";
    //$ausgabe .= '</div>';
    return $ausgabe;
  }

  public function getHGroupStart($class='mb-3', $style = ''){
    return '<div class="input-group '.$class.'" style="'.$style.'">';
  }

  public function getHGroupEnde(){
    return '</div>';
  }


}

?>
