<?php

namespace ITHilbert\LaravelKit\Classes;

use Illuminate\Support\Facades\Cache;

class TimeStop
{
    public static function start()
    {
        if (self::zeitstempel(Cache::get('TimeStop_start')) === null) {
            Cache::forever('TimeStop_start', now()->getTimestamp());
        }
    }

    public static function ende()
    {
        $start = self::zeitstempel(Cache::get('TimeStop_start'));
        Cache::forget('TimeStop_start');

        if ($start === null) {
            return 0;
        }

        return now()->getTimestamp() - $start;
    }

    /**
     * Gültig sind int und Ziffern-Strings (z. B. Redis ohne Serializer).
     */
    private static function zeitstempel(mixed $wert): ?int
    {
        if (! is_int($wert) && ! (is_string($wert) && ctype_digit($wert))) {
            return null;
        }

        $zahl = filter_var($wert, FILTER_VALIDATE_INT);

        return $zahl === false ? null : $zahl;
    }
}
