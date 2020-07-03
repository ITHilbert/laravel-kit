<?php

namespace ITHilbert\LaravelKit\Entities;

use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
    protected $table ='log';
    protected $fillable = ['log'];
    public $timestamps = true;

    public static function write($text){
        $log = new Log;
        $log->log = $text;
        $log->save();
    }

}
