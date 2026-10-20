<?php

namespace ITHilbert\LaravelKit\Helpers;

class hLogger
{
    public static function Log($text)
    {
        $verzeichnis = storage_path('logs/hLogger');

        // Verzeichnis bei Bedarf anlegen; parallele Anfragen können sich überholen
        if (! is_dir($verzeichnis) && ! @mkdir($verzeichnis, 0775, true) && ! is_dir($verzeichnis)) {
            return false;
        }

        return error_log($text."\n", 3, $verzeichnis.'/'.date('Y-m-d').'.txt');
    }
}
