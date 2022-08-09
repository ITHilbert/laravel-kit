<?php
namespace ITHilbert\LaravelKit\Helpers;

class hLogger{
    public static function Log($text){
        return error_log($text ."\n" , 3, storage_path('/logs/hLogger/'. date('yy-m-d') .'.txt') );
    }
}
