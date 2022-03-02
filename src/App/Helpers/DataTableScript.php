<?php

namespace ITHilbert\LaravelKit\App\Helpers;

class DataTableScript{

    //Einstellungen
    public $bFilter = "true";             //Filtern erlauben
    public $bLengthChange = "false";      //Anzahl der Datensätze ändern
    public $languageJsonPath = "vendor/laravelkit/DataTable_DE.json";        //Deutsche Bezeichnungen für die Tabelle
    public $processing = "true";
    public $serverSide = "true";
    public $pageLength;                 //Anzahl der Zeilen in der Liste
    public $sortcolumn = 1;             //Spalte nach der Sortiert werden soll
    public $sortArt = 'asc';             //Reihenfolge beim Sortieren

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
        $this->pageLength = 10;
    }

    /**
     * Spalten hinzufügen
     *
     * @param [string] $column
     * @return void
     */
    public function addColumn($data, $name = ''){
        if($name == '') $name = $data;

        $this->columns[] = (object) ['name' => $name, 'data' => $data];
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
        $ausgabe .= 'pageLength: '. $this->pageLength .",\n";
        $ausgabe .= 'order: [[ '.$this->sortcolumn.', "'. $this->sortArt .'"]],'."\n";
        $ausgabe .= 'searching: true,'."\n";

        return $ausgabe;
    }

    /**
     * Liefert das Script zum Filtern
     *
     * @return string
     */
    public function getFilterScript(){
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
        //dd($column);
        $row = "        { data: '".$column->data ."', name: '".$column->name ."', defaultContent: ''},\n";

        return $row;
    }


}
