<?php
namespace ITHilbert\LaravelKit\App\Classes;

class MyEnv
{
    public static function setValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $oldValue = env($envKey);

        //Prüfen ob noch nicht vorhanden
        if(is_null(env($envKey))){
            return self::addKey($envKey, $envValue);
        }

        //Passenden Eintrag suchen und ändern
        $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}\n", $str);
        $str = str_replace("{$envKey}='{$oldValue}'", "{$envKey}='{$envValue}'\n", $str);
        $str = str_replace("{$envKey}=\"{$oldValue}\"", "{$envKey}=\"{$envValue}\"\n", $str);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

    public static function addKey($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        //Prüfen ob bereits vorhanden
        if(!is_null(env($envKey))){
            return self::setValue($envKey, $envValue);
        }

        $str .= "{$envKey}='{$envValue}'\n";

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

}
