<?php

namespace ITHilbert\LaravelKit\Helpers;

/**
 * Klasse erstellt das JavaScript für die DataTable
 */
class DataTableScript_alt{

    //Einstellungen
    public $bFilter = "true";             //Filtern erlauben
    public $bLengthChange = "false";      //Anzahl der Datensätze ändern
    public $languageJsonPath = "vendor/laravelkit/DataTable_DE.json";        //Deutsche Bezeichnungen für die Tabelle
    public $processing = "true";
    public $serverSide = "true";
    public $pageLength = 15;             //Anzahl der Zeilen in der Liste
    public $ordercolumn = 1;             //Spalte nach der Sortiert werden soll


    public $filterScript = true;         //Soll das Filter Script mit ausgeben werden

    public $columns = array();          //Spalten der Tabelle
    public $route;                      //Woher sollen die Daten geladen werden

    /**
     * Constructor
     *
     * @param [strin] $route
     */
    public function __construct($route){
        $this->route= $route;
    }

    /**
     * Spalten hinzufügen
     *
     * @param [string] $column
     * @return void
     */
    public function addColumn($column){
        $this->columns[] = $column;
    }

    /**
     * Liefert das JavaScript
     *
     * @return string
     */
    public function getScript(){
        $ausgabe = "var table = $('.data-table').DataTable({\n";
        $ausgabe .= $this->getSettings();
        $ausgabe .= "    ajax: '" . $this->route . "',\n";
        $ausgabe .= "    columns: [\n";

        foreach($this->columns as $column ){
            $ausgabe .= $this->getScriptDataRow($column);
        }
        $ausgabe .= "{ data: 'action', name: 'action', orderable: false, searchable: false },\n";
        $ausgabe .= "   ],\n";
        $ausgabe .= "});\n";

        if($this->filterScript){
            $ausgabe .= $this->getFilterScript();
        }

        return $ausgabe;
    }


    /**
     * Liefert die Einstellungen der globalen DataTabel einstellungen aus App\Helpers\DataTableScript
     *
     * @return string
     */
    public function getSettings(){
        $ausgabe =  'processing: '. $this->processing .','."\n";
        $ausgabe .= 'serverSide: '. $this->serverSide .','."\n";
        $ausgabe .= 'bFilter: '. $this->bFilter  .','."\n";
        $ausgabe .= 'bLengthChange: ' . $this->bLengthChange .','."\n";
        $ausgabe .= 'language: { url: "'. asset($this->languageJsonPath).'" },'."\n";
        //$ausgabe .= 'pageLength: '. $this->pageLength .",\n";
        //$ausgabe .= 'order: [[ '.$this->ordercolumn.", 'asc' ]],\n";
        $ausgabe .= 'searching: true,'."\n";

        return $ausgabe;
    }

    /**
     * Liefert das Script zum Filtern
     *
     * @return string
     */
    public function getFilterScript(){
        //DataTable Filtern
        $ausgabe = "$( '.filter' ).on( 'keyup change', function () {\n";
        $ausgabe .=  "    let i = $(this).attr('data-column')-1\n";
        $ausgabe .=  "    console.log(i + ' - ' + this.value)\n";
        $ausgabe .=  "    if ( table.column(i).search() !== this.value ) {\n";
        $ausgabe .=  "    table\n";
        $ausgabe .=  "        .column(i)\n";
        $ausgabe .=  "        .search( this.value )\n";
        $ausgabe .=  "        .draw();\n";
        $ausgabe .=  "    }\n";
        $ausgabe .=  "} );\n";

        //Filter zurücksetzen
        $ausgabe .=  "$('.filterclear').on('click', function(){\n";
        $ausgabe .=  "    $('.filter').val('').change();\n";
        $ausgabe .=  "});\n";

        return $ausgabe;
    }

    /**
     * Liefert einen Data Part des Scripts
     *
     * @param [type] $column
     * @return void
     */
    private function getScriptDataRow($column){
        $row = "        { data: '".$column."', name: '".$column ."' },\n";
        /* if($column->orderable == false ){
            $row .= ", orderable: false";
        }
        if($column->searchable == false ){
            $row .= ", searchable: false";
        } */
        //$row .= " }, \n";

        return $row;
    }


}
