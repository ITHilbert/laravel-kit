<?php
namespace ITHilbert\LaravelKit\Classes;

use Illuminate\Support\Facades\Cache;

class TimeStop
{

    public static function start()
    {
        Cache::rememberForever('TimeStop_start', function () {
            return now();
        });
    }

    public static function ende()
    {
        $start = Cache::get('TimeStop_start');
        $ende = now();

        //echo 'start:' . strtotime($ende) . ' - ende: ' . strtotime($start).' = ' . strtotime($ende) - strtotime($start) . '<br>';
        $erg = strtotime($ende) - strtotime($start);
        Cache::forget('TimeStop_start');

        return $erg;
    }

}
