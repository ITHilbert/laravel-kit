<?php
namespace ITHilbert\LaravelKit\Helpers;

class hLogger{

    private static $instance = null;

    public static function getInstance(){
      if(self::$instance == null){
        self::$instance = new hLogger();
      }
      return self::$instance;
    }

    protected function __construct(){}
    private function __clone(){}


    public function Log($text){
        return error_log($text ."\n" , 3, storage_path('/logs/hLogger/'. date('yy-m-d') .'.txt') );
    }


}
