<?php

namespace ITHilbert\LaravelKit\Helpers;

class DataTableScript{

    //Einstellungen
    public $tableID = ".data-table";    //Die Id der Tabelle oder Klasse auf die sich das Script bezieht
    public $bFilter = "true";           //Filtern erlauben
    public $filterScript = true;        //Soll das Filter Script mit ausgeben werden
    public $bLengthChange = "false";    //Anzahl der Datensätze ändern
    public $pageLength;                 //Anzahl der Zeilen in der Liste
    public $processing = "true";
    public $serverSide = "true";
    public $responsive = "true";
    public $sortcolumn = 1;             //Spalte nach der Sortiert werden soll
    public $sortArt = 'asc';            //Reihenfolge beim Sortieren
    public $orderable = true;           //Datensätze sortierbar
    public $paging = true;              //Paging true oder false

    public $languageJsonPath = "vendor/laravelkit/DataTable_DE.json";        //Deutsche Bezeichnungen für die Tabelle
    public $columns = array();          //Spalten der Tabelle
    public $route;                      //Woher sollen die Daten geladen werden


    /**
     * Erstellt eine neue Instanz des DataTableScript-Objekts.
     *
     * @param [string] $route
     * @return DataTableScript
     */
    public static function make($route)
    {
        return new self($route);
    }


    /**
     * Constructor -lädt die Werte aus der config/datatables.php
     *
     * @param [string] $route
     */
    public function __construct($route){
        $this->route= $route;

        $this->tableID = config('datatablescript.tableID', '.data-table');
        $this->bFilter = config('datatablescript.bFilter', true);
        $this->filterScript = config('datatablescript.filterScript', true);
        $this->bLengthChange = config('datatablescript.bLengthChange', false);
        $this->pageLength = config('datatablescript.pageLength', 10);
        $this->processing = config('datatablescript.processing', true);
        $this->serverSide = config('datatablescript.serverSide', true);
        $this->responsive = config('datatablescript.responsive', true);
        $this->sortcolumn = config('datatablescript.sortcolumn', 1);
        $this->sortArt = config('datatablescript.sortArt', 'asc');
        $this->orderable = config('datatablescript.orderable', true);
        $this->paging = config('datatablescript.paging', true);
        $this->languageJsonPath = config('datatablescript.languageJsonPath', 'vendor/laravelkit/DataTable_DE.json');
    }

    /**
     * Spalten hinzufügen
     *
     * @param [string] $column
     * @return void
     */
    public function addColumn($data, $name = '', $param = ''){
        if($name == '') $name = $data;

        $this->columns[] = (object) ['name' => $name, 'data' => $data, 'param' => $param];
    }

    /**
     * Sortiert die Daten
     *
     * @param [type] $spalte
     * @param string $reihenfolge
     * @return void
     */
    public function setOrder($spalte, $reihenfolge = 'asc'){
        $this->sortcolumn = $spalte;
        $this->sortArt = $reihenfolge;
    }

    /**
     * Liefert das JavaScript
     *
     * @return string
     */
    public function getScript(){
        $ausgabe = "var table = $('". $this->tableID ."').DataTable({\n";
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
     * Liefert die Einstellungen der globalen DataTabel einstellungen aus config/datatables.php
     *
     * @return string
     */
    private function getSettings(){
        $ausgabe =  'processing: ' . ($this->processing ? 'true' : 'false') . ",\n";
        $ausgabe .= 'serverSide: '. ($this->serverSide ? 'true' : 'false') .','."\n";
        $ausgabe .= 'responsive: '. ($this->responsive ? 'true' : 'false') .','."\n";
        $ausgabe .= 'bFilter: '. ($this->bFilter ? 'true' : 'false') .','."\n";
        $ausgabe .= 'language: { url: "'. asset($this->languageJsonPath).'" },'."\n";
        //Paging
        if($this->paging){
            $ausgabe .= 'bLengthChange: ' . ($this->bLengthChange ? 'true' : 'false') .','."\n";
            $ausgabe .= 'pageLength: '. $this->pageLength .",\n";
        }else{
            $ausgabe .= "paging: false,\n";
            $ausgabe .= "bInfo: false, \n";
        }

        //Sortierung (Ordering) Default on
        if(!$this->orderable){
            $ausgabe .= "ordering: false, \n";
        }
        $ausgabe .= 'order: [[ '.$this->sortcolumn.', "'. $this->sortArt .'"]],'."\n";
        $ausgabe .= 'searching: true,'."\n";

        return $ausgabe;
    }

    /**
     * Liefert das Script zum Filtern
     *
     * @return string
     */
    private function getFilterScript(){
        //DataTable Filtern Allgemein
        $ausgabe = "$( '.filter' ).on( 'keyup change', function () {\n";
        $ausgabe .=  "    let i = $(this).attr('data-column')-1\n";
        //$ausgabe .=  "    console.log(i + ' - ' + this.value)\n";
        $ausgabe .=  "    if ( table.column(i).search() !== this.value ) {\n";
        $ausgabe .=  "    table\n";
        $ausgabe .=  "        .column(i)\n";
        $ausgabe .=  "        .search( this.value )\n";
        $ausgabe .=  "        .draw();\n";
        $ausgabe .=  "    }\n";
        $ausgabe .=  "} );\n";

        //DataTable Filtern Exact
        $ausgabe .= "$( '.filterExact' ).on( 'keyup change', function () {\n";
        $ausgabe .=  "    let i = $(this).attr('data-column')-1\n";
        //$ausgabe .=  "    console.log(i + ' - ' + this.value)\n";
        $ausgabe .=  "    if ( table.column(i).search() !== this.value ) {\n";
        $ausgabe .=  "      if ( this.value == '' ) {\n";
        $ausgabe .=  "          regex = '';\n";
        $ausgabe .=  "      }else{\n";
        $ausgabe .=  "          regex = '^' + this.value + '$';\n";
        $ausgabe .=  "      }\n";
        $ausgabe .=  "      table\n";
        $ausgabe .=  "        .column(i)\n";
        $ausgabe .=  "        .search( regex, true, false )\n";
        $ausgabe .=  "        .draw();\n";
        $ausgabe .=  "    }\n";
        $ausgabe .=  "} );\n";


        //Filter zurücksetzen
        $ausgabe .=  "$('.filterclear').on('click', function(){\n";
        $ausgabe .=  "    $('.filter').val('').change();\n";
        $ausgabe .=  "    $('.filterExact').val('').change();\n";
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
        $row = "        { data: '".$column->data ."', name: '".$column->name ."'";
        if($column->param != ''){
            $row .= ', ' . $column->param;
        }
        $row .= "},\n";

        return $row;
    }


}
