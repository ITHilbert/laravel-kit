<?php


namespace ITHilbert\LaravelKit\App\Helpers;

use DateTime;
use Exception;

//Sprach-/Gebietsschema festlegen
setlocale(LC_TIME, "de_DE.utf8");
date_default_timezone_set("Europe/Berlin");


/**
* Meine Datumsklasse für Datum/Zeit als Datentyp
* @version 1.0.0
**/
class MyDateTime{

  //Attribute
  private $timestamp = 0;
  private $type = 'DateTime';   //DateTime, Date, Time, Nothing
  private $outputFormat = 'ISO';    //DE, ISO, DB

  //Hilfsarrays
  public $tage = array(1=>"Montag", 2=>"Dienstag", 3=>"Mittwoch", 4=>"Donnerstag", 5=>"Freitag", 6=>"Samstag", 7=>"Sonntag");
  public $tageSmall = array(1=>"Mo", 2=>"Di", 3=>"Mi", 4=>"Do", 5=>"Fr", 6=>"Sa", 7=>"So");
  public $monate = array(1=>"Januar", 2=>"Februar", 3=>"M&auml;rz", 4=>"April", 5=>"Mai", 6=>"Juni", 7=>"Juli", 8=>"August", 9=>"September", 10=>"Oktober", 11=>"November", 12=>"Dezember");
  public $monateEN = array(1=>"January", 2=>"February", 3=>"March", 4=>"April", 5=>"May", 6=>"June", 7=>"July", 8=>"August", 9=>"September", 10=>"October", 11=>"November", 12=>"December");


  /************************************************************************
  * Konstruktor
  ************************************************************************/
  public function __construct($datumZeit=''){
    $this->setDateTime($datumZeit);
  }

  /**
    Datum und oder Zeit setzen
  **/
  public function setDateTime($datumZeit = ''){
    if($datumZeit == 'now'){
      $this->timestamp = time();
    }elseif($datumZeit == 'NULL'){
      $this->timestamp = 0;
    //2018-05-25 15:25:06
    }elseif(strpos($datumZeit, '-')>0 && strpos($datumZeit, ':')>0 && strpos($datumZeit, ' ')>0){
      $teil = explode(' ', $datumZeit);
      $datum = explode('-', $teil[0]);
      $zeit = explode(':', $teil[1]);
      if(count($zeit) == 3){
        $this->timestamp = date('U', mktime($zeit[0], $zeit[1], $zeit[2], $datum[1], $datum[2], $datum[0]));
      }else{
        $this->timestamp = date('U', mktime($zeit[0], $zeit[1], 0, $datum[1], $datum[2], $datum[0]));
      }
      $this->type = "DateTime";

    //25.05.2018 15:25:06
    }elseif(strpos($datumZeit, '.')>0 && strpos($datumZeit, ':')>0 && strpos($datumZeit, ' ')>0){
      $teil = explode(' ', $datumZeit);
      $datum = explode('.', $teil[0]);
      $zeit = explode(':', $teil[1]);
      if(count($zeit) == 3){
        $this->timestamp = date('U', mktime($zeit[0], $zeit[1], $zeit[2], $datum[1], $datum[0], $datum[2]));
      }else{
        $this->timestamp = date('U', mktime($zeit[0], $zeit[1], 0, $datum[1], $datum[0], $datum[2]));
      }
      $this->type = "DateTime";

    //2018-05-25
    }elseif(strpos($datumZeit, '-')>0){
      if($datumZeit != '0000-00-00'){
        $datum = explode('-', $datumZeit);
        $this->timestamp = date('U', mktime(0, 0 , 0, $datum[1], $datum[2], $datum[0]));
        $this->type = "Date";
      }else{
        $this->timestamp = 0;
        $this->type = "Date";
      }

    //25.05.2018
    }elseif(strpos($datumZeit, '.')>0){
      $datum = explode('.', $datumZeit);
      $this->timestamp = date('U', mktime(0, 0, 0, $datum[1], $datum[0], $datum[2]));
      $this->type = "Date";

    //15:25:06 oder 15:25
    }elseif(strpos($datumZeit, ':')>0){
      $zeit = explode(':', $datumZeit);
      if(count($zeit) == 3){
        $this->timestamp = date('U', mktime($zeit[0], $zeit[1], $zeit[2],0,0,0));
      }else{
        $this->timestamp = date('U', mktime($zeit[0], $zeit[1], 0,0,0,0));
      }
      $this->type = "Time";

    //1537451550
    }elseif($this->isTimestamp($datumZeit)){
      $this->timestamp = $datumZeit;
      $this->type = "DateTime";
    }else{
      $this->timestamp = 0;
      $this->type = "Nothing";
    }

    //echo $datumZeit . ' -> ' . $this->timestamp .' -> ' . $this->type.  '<br />';
  }

  /************************************************************************
  * toString
  ************************************************************************/
  public function __toString(){
    //Ausgabe deutsches Format
    if($this->outputFormat == 'DE'){
      if($this->type == 'DateTime'){
        return $this->getDateTimeDE();
      }elseif($this->type == 'Date'){
        return $this->getDateDE();
      }elseif($this->type == 'Time'){
        return $this->getTime();
      }else{
        return false;
      }
    //Ausgabe Datenbankformat
    }elseif($this->outputFormat == 'DB'){
      if($this->type == 'DateTime'){
        return $this->getDateTimeDB();
      }elseif($this->type == 'Date'){
        return $this->getDateDB();
      }elseif($this->type == 'Time'){
        return $this->getTime();
      }else{
        return false;
      }
    //Ausgabe nach ISO
    }else{
      if($this->type == 'DateTime'){
        return $this->getDateTimeISO();
      }elseif($this->type == 'Date'){
        return $this->getDateISO();
      }elseif($this->type == 'Time'){
        return $this->getTime();
      }else{
        return false;
      }
    }
  }

  /**
  * Gibt das Ausgabeformat zurück (DE, DB, ISO)
  * @return this
  **/
  public function setOutputFormat($format){
    $this->outputFormat = $format;
   return $this;
  }



  //#################### Ausgabe Funktionen #################################

  /**
  * Gibt den Timestamp formatiert zurück
  * @param string $string
  * @return string
  **/
  public function format($format){
   return date($format, $this->timestamp);
  }


  /**
  * Gibt den Timestamp zurück
  * @return string
  **/
  public function getTimestamp(){
   return $this->timestamp;
  }

  /**
  * Gibt den Typ vom Timestamp zurück (DateTime, Date, Time, Nothing)
  * @return string
  **/
  public function getType(){
   return $this->type;
  }

  /**
  * Gibt das Ausgabeformat zurück (DE, DB, ISO)
  * @return string
  **/
  public function getOutputFormat(){
   return $this->outputFormat;
  }



  /**
  * Gibt den Timestamp als Datum und Uhrzeit zurück
  * @param string $format DE, DB, ISO
  * @return string
  **/
  public function getDateTime($format = ''){
    //Format festlegen
    if($format== ''){
      $format = $this->outputFormat;
    }
    //Ausgabe
    if($format == 'DE'){
      return date('d.m.Y H:i:s', $this->timestamp);
    }elseif($format == 'DB'){
      return date('YmdHis', $this->timestamp);
    }else{
      return date('Y-m-d H:i:s', $this->timestamp);
    }
  }

  /**
  * Gibt den Timestamp als DE Datum und Uhrzeit zurück
  * @return string
  **/
  public function getDateTimeDE(){
    return date('d.m.Y H:i:s', $this->timestamp);
  }

  /**
  * Gibt den Timestamp als ISO Datum und Uhrzeit zurück
  * @return string
  **/
  public function getDateTimeISO(){
    return date('Y-m-d H:i:s', $this->timestamp);
  }

  /**
  * Gibt den Timestamp als Datenbank Format Datum und Uhrzeit zurück
  * @return string
  **/
  public function getDateTimeDB(){
    return date('YmdHis', $this->timestamp);
  }

  /**
  * Gibt den Timestamp als Datum zurück
  * @return string
  **/
  public function getDate($format = ''){
    //Format festlegen
    if($format== ''){
      $format = $this->outputFormat;
    }
    //Ausgabe
    if($format == 'DE'){
      return $this->getDateDE();
    }elseif($format == 'DB'){
      return $this->getDateDB();
    }else{
      return $this->getDateISO();
    }

  }

  /**
  * Gibt den Timestamp als DE Datum zurück
  * @return string
  **/
  public function getDateDE(){
    if($this->timestamp != 0){
      return date('d.m.Y', $this->timestamp);
    }else{
      return '';
    }
  }

  /**
  * Gibt den Timestamp als ISO Datum zurück
  * @return string
  **/
  public function getDateISO(){
    return date('Y-m-d', $this->timestamp);
  }

  /**
  * Gibt den Timestamp als Datenbank Format Datum zurück
  * @return string
  **/
  public function getDateDB(){
    if($this->timestamp != 0){
      return date('Ymd', $this->timestamp);
    }else{
      return '0000-00-00';
    }
  }

  /**
  * Gibt den Timestamp als Uhrzeit mit Secunden zurück
  * @return string
  **/
  public function getTime(){
   return date('H:i:s', $this->timestamp);
  }

  /**
  * Gibt den Timestamp als Uhrzeit ohne Secunden zurück
  * @return string
  **/
  public function getTimeSmall(){
    return date('H:i', $this->timestamp);
  }

  /**
  * Gibt den Timestamp als Uhrzeit im Datenbank Format zurück
  * @return string
  **/
  public function getTimeDB(){
    if($this->timestamp != 0){
      return date('H:i', $this->timestamp);
    }else{
      return '00:00';
    }
  }

  /**
  * Gibt den Tag der Woche zurück (als Zahl 1 = Montag 7 = Sonntag)
  * @return int
  **/
  public function getDayOfWeek(){
   return date('N', $this->timestamp);
  }

  /**
  * Gibt den Tag den Wochentag zurück (Montag bis So)
  * @return string
  **/
  public function getDayOfWeekName(){
   return $this->tage[date('N', $this->timestamp)];
  }

  /**
  * Gibt den Tag den Wochentag in Kurzform zurück (Mo bis So)
  * @return string
  **/
  public function getDayOfWeekNameSmall(){
   return $this->tageSmall[date('N', $this->timestamp)];
  }


  /**
  * Gibt den Tag des Jahres zurück (0 bis 365)
  * @return int
  **/
  public function getDayOfYear(){
   return date('z', $this->timestamp);
  }

  /**
  * Gibt die Kalenderwoche zurück (1 = Montag 7 = Soontag)
  * @return int
  **/
  public function getKW(){
   return date('W', $this->timestamp);
  }

  /**
  * Gibt die Stunde zurück
  * @return int
  **/
  public function getHour(){
   return date('H', $this->timestamp);
  }

  /**
  * Gibt die Minuten zurück
  * @return int
  **/
  public function getMin(){
   return date('i', $this->timestamp);
  }

  /**
  * Gibt die Secunden zurück
  * @return int
  **/
  public function getSec(){
   return date('s', $this->timestamp);
  }

  /**
  * Gibt den Tag zurück
  * @return int
  **/
  public function getDay(){
   return date('d', $this->timestamp);
  }

  /**
  * Gibt den Monat zurück
  * @return int
  **/
  public function getMonth(){
   return date('m', $this->timestamp);
  }

  /**
  * Gibt den Monatsnamen zurück
  * @return string
  **/
  public function getMonthName(){
   return date('F', $this->timestamp);
  }

  /**
  * Gibt den Monatsnamen mit 3 Zeichen zurück
  * @return string
  **/
  public function getMonthNameSmall(){
   return date('M', $this->timestamp);
  }

  /**
  * Gibt das Jahr zurück
  * @return int
  **/
  public function getYear(){
   return date('Y', $this->timestamp);
  }


  //#################### Prüf Funktionen #################################

    /**
    * Prüft ob die Variable ein Datum ist
    * @param string $string
    * @return bool
    **/
    public function is_Date($str){
      try{
        $str=str_replace('/', '-', $str);  //see explanation below for this replacement
        return is_numeric(strtotime($str));
      }catch (\Exception $e) {
          return false;
      }
    }

   /**
   * Prüft ob die Variable ein Stimestamp ist
   * @param string $string
   * @return bool
   **/
    public function isTimestamp($string)
    {
        try {
            new DateTime('@' . $string);
        } catch(Exception $e) {
            return false;
        }
        return true;
    }


  /**
  * Prüft ob das Jahr ein Schaltjahr ist (0 = nein 1 = ja)
  * @return int
  **/
  public function isSchaltJahr(){
   return date('L', $this->timestamp);
  }

  /**
  * Prüft ob die Zeit in die Sommer Zeit fällt (0 = nein 1 = ja)
  * @return int
  **/
  public function isSommerTime(){
   return date('I', $this->timestamp);
  }


  //#################### ADD Funktionen #################################

  /**
  * Addiert Secunden zum timestamp
  * @param int $sec
  * @return $this
  **/
  public function addSec($sec){
    $this->timestamp += $sec;
    return $this;
  }

  /**
  * Addiert Minuten zum timestamp
  * @param int $min
  * @return $this
  **/
  public function addMin($min){
    $this->timestamp += 60 * $min;
    return $this;
  }

  /**
  * Addiert Stunden zum timestamp
  * @param int $std
  * @return $this
  **/
  public function addHour($std){
    $this->timestamp += 60 * 60 * $std;
    return $this;
  }

  /**
  * Addiert Tage zum timestamp
  * @param int $day
  * @return $this
  **/
  public function addDay($day){
    $this->timestamp += 60 * 60 * 24 * $day;
    return $this;
  }

  /**
  * Addiert Wochen zum timestamp
  * @param int $week
  * @return $this
  **/
  public function addWeek($week){
    $this->timestamp += 60 * 60 * 24 * 7 * $week;
    return $this;
  }

  /**
  * Addiert Jahre zum timestamp
  * @param int $year
  * @return $this
  **/
  public function addYear($year){
    //Jahre hinzu
    if($year >= 0){
      //Alle Jahre durchgehen
      for($y = 1; $y <= $year; $y++){
        //Immer 365 Tage hinzu addieren
        $this->timestamp += 60 * 60 * 24 * 365;

        //Wenn das neue Datum in einem Schaltjahr liegt,
        //dann einen Tag hinzu addieren
        if($this->isSchaltJahr() == 1){
          $this->timestamp += 60 * 60 * 24;
        }
      }
      //Wenn das Letzte Datum in einem Schaltjahr liegt und
      //der Monat kleiner gleich 2 ist, dann
      //1 Tag wieder abziehen
      if($this->isSchaltJahr() == 1 && $this->getMonth()<=2){
        $this->timestamp -= 60 * 60 * 24;
      }
    //Jahre Abziehen
    }else{
      //Alle durchlaufen
      for($y = ($year * -1); $y > 0; $y--){
        //Immer 365 Tage abziehen
        $this->timestamp -= 60 * 60 * 24 * 365;

        //Wenn das Jahr davor ein Schaltjahr ist, dann einen weiteren Tag abziehen
        if($this->isSchaltJahr() == 1){
          $this->timestamp -= 60 * 60 * 24;
        }
      }
      //Wenn das letzte Datum ein Schaltjahr ist
      //und der Monat größer 2 ist,
      //dann muss ein Tag wieder drauf.
      if($this->isSchaltJahr() == 1 && $this->getMonth()>2){
        $this->timestamp += 60 * 60 * 24;
      }
    }
    return $this;
  }



  //#################### weitere Funktionen #################################



  /**
  * Berechnet die Differenz zwischen zwei Datums angaben
  * @param MyDateTime $datumZeit
  * @param string $differenceFormat Wie soll das Ergebniss formatiert sein
  * @return $this
  **/
  //////////////////////////////////////////////////////////////////////
  //PARA: Date Should In YYYY-MM-DD Format
  //RESULT FORMAT:
  // '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'      =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
  // '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
  // '%m Month %d Day'                                            =>  3 Month 14 Day
  // '%d Day %h Hours'                                            =>  14 Day 11 Hours
  // '%d Day'                                                     =>  14 Days
  // '%h Hours %i Minute %s Seconds'                              =>  11 Hours 49 Minute 36 Seconds
  // '%i Minute %s Seconds'                                       =>  49 Minute 36 Seconds
  // '%h Hours                                                    =>  11 Hours
  // '%a Days                                                     =>  468 Days
  //////////////////////////////////////////////////////////////////////
  public function diff(MyDateTime $datumZeit, $differenceFormat = '%a' ){
    $diff = date_diff(date_create($this->getDateTimeISO()),date_create($datumZeit->getDateTimeISO()));
    return $diff->format($differenceFormat);
  }

  /**
  * Berechnet die Differenz zwischen zwei Datums angaben
  * @param string $datum  Datum was in MyDateTime umgewandelt wird
  * @param string $differenceFormat Wie soll das Ergebniss formatiert sein
  * @return $this
  **/
  //////////////////////////////////////////////////////////////////////
  //PARA: Date Should In YYYY-MM-DD Format
  //RESULT FORMAT:
  // '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'      =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
  // '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
  // '%m Month %d Day'                                            =>  3 Month 14 Day
  // '%d Day %h Hours'                                            =>  14 Day 11 Hours
  // '%d Day'                                                     =>  14 Days
  // '%h Hours %i Minute %s Seconds'                              =>  11 Hours 49 Minute 36 Seconds
  // '%i Minute %s Seconds'                                       =>  49 Minute 36 Seconds
  // '%h Hours                                                    =>  11 Hours
  // '%a Days                                                     =>  468 Days
  //////////////////////////////////////////////////////////////////////
  public function diff2($datum, $differenceFormat = '%a' ){
    $datumZeit = new MyDateTime($datum);
    $diff = date_diff(date_create($this->getDateTimeISO()),date_create($datumZeit->getDateTimeISO()));
    return $diff->format($differenceFormat);
  }




  /**
  * Gibt das Datum für den ersten Tag in der KW (Montag) zurück
  * @param int $kw Nummer der Kalenderwoche
  * @param int $jahr In welchen Jahr (Standard aktuelles Jahr)
  * @return Date Y-m-d
  **/
  public function getKWMo($kw, $jahr=0){
    if($jahr == 0){
      $jahr = date("Y");
    }

    //Die einfachste Grundlage zur Berechnung liefert diese Folgerung:
    //Der 4. Januar liegt immer in der ersten Kalenderwoche; der 28. Dezember davor immer in der letzten KW des Vorjahres. Ergo:
    //list($jahr, $kw) = explode('-', $datum);
    // man nehme den 28.12. des Vorjahres:
    $t = mktime(12, 0, 0, 12, 28, $jahr-1);
    // packe $kw Wochen drauf
    $t += $kw * 604800; // 7*24*3600 = 604800 Sekunden/Woche
    // und ermittle den dazugehörigen Wochentag
    $w = date('w', $t);
    // der Sonntag liefert 0, benötigt wird die 7
    if (!$w) $w = 7;
    // um zum Montag zu kommen, müssen $w-1 Tage abgezogen werden:
    $t -= 86400 * ($w-1);

    return date("Y-m-d",$t);
  }


  /**
  * Gibt das Datum für den letzten Tag in der KW (Sonntag) zurück
  * @param int $kw Nummer der Kalenderwoche
  * @param int $jahr In welchen Jahr (Standard aktuelles Jahr)
  * @return Date Y-m-d
  **/
  public function getKWSo($kw, $jahr=0){
    if($jahr == 0){
      $jahr = date("Y");
    }

    //Die einfachste Grundlage zur Berechnung liefert diese Folgerung:
    //Der 4. Januar liegt immer in der ersten Kalenderwoche; der 28. Dezember davor immer in der letzten KW des Vorjahres. Ergo:
    //list($jahr, $kw) = explode('-', $datum);
    // man nehme den 28.12. des Vorjahres:
    $t = mktime(12, 0, 0, 12, 28, $jahr-1);
    // packe $kw Wochen drauf
    $t += $kw * 604800; // 7*24*3600 = 604800 Sekunden/Woche
    // und ermittle den dazugehörigen Wochentag
    $w = date('w', $t);
    // der Sonntag liefert 0, benötigt wird die 7
    if (!$w) $w = 7;
    // um zum Montag zu kommen, müssen $w-1 Tage abgezogen werden:
    $t -= 86400 * ($w-1);
    //Wochenende ermitteln
    $t += 604800 - 86400;

    return date("Y-m-d",$t);
  }


    public function getAlter(){
        try {
          $alter = $this->diff(new MyDateTime('now') , '%y');
        } catch (Exception $exc) {
          $alter = 'na';
        }

        return $alter;
    }


}
