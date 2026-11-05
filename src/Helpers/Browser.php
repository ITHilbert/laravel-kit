<?php

namespace ITHilbert\LaravelKit\Helpers;

use Exception;

class Browser
{
    public static function getSprache()
    {
        // dd($_SERVER);
        try {
            $sprache = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
            if (strlen($sprache) > 5) {
                $sprache = substr($sprache, 0, 5);
            }

            return $sprache;

        } catch (Exception $ex) {
            return 'unbekannt';
        }
    }

    public static function getSpracheLong()
    {
        if (! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // "Linux", ...
            return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        return 'N/A';
    }

    public static function getIP()
    {
        // Trusted Proxies des Hosts beachten, keine Kopfzeilen direkt uebernehmen
        return request()->ip() ?? 'N/A';
    }

    public static function getPlatform()
    {
        if (! empty($_SERVER['HTTP_SEC_CH_UA_PLATFORM'])) {
            // "Linux", ...
            return str_replace('"', '', $_SERVER['HTTP_SEC_CH_UA_PLATFORM']);
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $plattform = 'N/A';

        $plattforms = [
            '/msie/i' => 'Windows',
            '/Windows/i' => 'Windows',
            '/Android/i' => 'Android',
            '/iPhone/i' => 'iPhone',
            '/Mac OS/i' => 'Mac OS',
        ];

        foreach ($plattforms as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $plattform = $value;
            }
        }

        return $plattform;
    }

    public static function getAgent()
    {
        if (! empty($_SERVER['HTTP_USER_AGENT'])) {
            // Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.61 Safari/537.36
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return 'N/A';
    }

    public static function getMobile()
    {
        if (! empty($_SERVER['HTTP_SEC_CH_UA_MOBILE'])) {
            // Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.61 Safari/537.36
            return $_SERVER['HTTP_SEC_CH_UA_MOBILE'];
        }

        return 'N/A';
    }

    public static function isRobot()
    {
        $browser = self::getBrowser();
        $whiteListe = ['Internet explorer', 'Firefox', 'Safari', 'Chrome', 'Edge', 'Opera', 'Mobile browser'];

        return ! in_array($browser, $whiteListe);
    }

    public static function isMobil()
    {
        $plattform = self::getPlatform();
        $whiteListe = ['Android', 'iPhone'];

        return in_array($plattform, $whiteListe);
    }

    public static function getBrowser()
    {

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $browser = 'N/A';

        $browsers = [
            '/msie/i' => 'Internet explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/mobile/i' => 'Mobile browser',
            '/robot/i' => 'robot',
            '/spider/i' => 'spider',
            '/crawler/i' => 'crawler',
            '/bingbot/i' => 'bingbot',
            '/SemrushBot/i' => 'SemrushBot',
            '/DotBot/i' => 'DotBot',
            '/AhrefsBot/i' => 'AhrefsBot',
            '/MJ12bot/i' => 'MJ12bot',
            '/AdsBot-Google/i' => 'Google-AdsBot',
            '/Googlebot/i' => 'Googlebot',
            '/facebookexternalhit/i' => 'Facebook',
            '/ZoominfoBot/i' => 'ZoominfoBot',
            '/DuckDuckGo/i' => 'DuckDuckGo',
        ];

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }

    public static function isGoogleAdsCheck()
    {

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $allows = [
            '/Google-AdWords-Express/i',
            '/Google-AdWords/i',
        ];

        foreach ($allows as $regex) {
            if (preg_match($regex, $user_agent)) {
                return true;
            }
        }

        return false;
    }

    public static function getURL()
    {
        return self::urlZuPfad(url()->current());
    }

    public static function getURLBevor()
    {
        return self::urlZuPfad(url()->previous());
    }

    private static function urlZuPfad(string $url): string
    {
        $pfad = parse_url($url, PHP_URL_PATH);
        if (! is_string($pfad) || $pfad === '' || $pfad === '/') {
            return 'index';
        }

        $query = parse_url($url, PHP_URL_QUERY);

        return is_string($query) && $query !== '' ? $pfad.'?'.$query : $pfad;
    }

    public static function getID()
    {
        $id = self::getIP();
        $id .= self::getPlatform();
        $id .= self::getAgent();
        $id .= self::getSpracheLong();

        if (strlen($id) > 255) {
            $id = substr($id, 0, 254);
        }

        return $id;
    }
}
