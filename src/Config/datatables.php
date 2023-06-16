<?php
/**
 * Einstellungen zu den DataTables
 */
return [
    'bFilter' => true,                //Filtern erlauben
    'filterScript' => true,           //Soll das Filter Script mit ausgeben werden

    'bLengthChange' => false,         //Anzahl der Datensätze ändern
    'pageLength' => 25,               //Anzahl der Zeilen in der Liste

    'processing' => true,
    'serverSide' => true,
    'responsive' => true,             //Tabellen Responsive machen

    'sortcolumn' => 1,                //Spalte nach der Sortiert werden soll
    'sortArt' => 'asc',               //Reihenfolge beim Sortieren
    'orderable' => true,              //Datensätze sortierbar

    'paging' => true,                 //Paging true oder false

    'languageJsonPath' => "vendor/laravelkit/DataTable_DE.json",        //Deutsche Bezeichnungen für die Tabelle
];
