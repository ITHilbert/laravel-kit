<?php

namespace ITHilbert\LaravelKit\Classes;

use Illuminate\Support\Facades\Cache;

class TimeStop
{
    public static function start()
    {
        $vorhanden = Cache::get('TimeStop_start');

        if (! is_int($vorhanden)) {
            Cache::forever('TimeStop_start', now()->getTimestamp());
        }
    }

    public static function ende()
    {
        $start = Cache::get('TimeStop_start');
        Cache::forget('TimeStop_start');

        if (! is_int($start)) {
            return 0;
        }

        return now()->getTimestamp() - $start;
    }
}
