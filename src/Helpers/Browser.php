<?php
namespace ITHilbert\LaravelKit\Helpers;

use Exception;

class Browser{

    public static function getSprache(){
        //dd($_SERVER);
        try {
            $sprache = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
            if(Strlen($sprache) > 5){
                $sprache = substr($sprache, 0, 5);
            }
            return $sprache;

        } catch (Exception $ex) {
            return 'unbekannt';
        }
    }

    public static function getSpracheLong(){
        if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            //"Linux", ...
            return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        return 'N/A';
    }

    public static function getIP(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getPlatform(){
        if(!empty($_SERVER['HTTP_SEC_CH_UA_PLATFORM'])){
            //"Linux", ...
            return  str_replace('"','', $_SERVER['HTTP_SEC_CH_UA_PLATFORM']);
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $plattform = "N/A";

        $plattforms = array(
        '/msie/i' => 'Windows',
        '/Windows/i' => 'Windows',
        '/Android/i' => 'Android',
        '/iPhone/i' => 'iPhone',
        '/Mac OS/i' => 'Mac OS'
        );

        foreach ($plattforms as $regex => $value) {
            if (preg_match($regex, $user_agent)) { $plattform = $value; }
        }

        return $plattform;
    }




    public static function getAgent(){
        if(!empty($_SERVER['HTTP_USER_AGENT'])){
            //Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.61 Safari/537.36
            return $_SERVER['HTTP_USER_AGENT'];
        }
        return 'N/A';
    }

    public static function getMobile(){
        if(!empty($_SERVER['HTTP_SEC_CH_UA_MOBILE'])){
            //Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.5005.61 Safari/537.36
            return $_SERVER['HTTP_SEC_CH_UA_MOBILE'];
        }
        return 'N/A';
    }

    public static function isRobot(){
        $browser = self::getBrowser();
        $whiteListe = ["Internet explorer", "Firefox", "Safarie", "Chrome", "Edge", "Opera", "Mobile browser"];
        return !in_array($browser, $whiteListe);
    }

    public static function isMobil(){
        $plattform = self::getPlatform();
        $whiteListe = ["Android", "iPhone"];
        return in_array($plattform, $whiteListe);
    }

    public static function getBrowser() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = "N/A";

        $browsers = array(
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
        '/DuckDuckGo/i' => 'DuckDuckGo'
        );

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, $user_agent)) { $browser = $value; }
        }

        return $browser;
    }

    public static function isGoogleAdsCheck() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $allows = array(
        '/Google-AdWords-Express/i',
        '/Google-AdWords/i',
        );

        foreach ($allows as $regex ) {
            if (preg_match($regex, $user_agent)) { return true; }
        }

        return false;
    }


    public static function getURL(){
        $url = url()->current() ?? 'N/A';
        if($url == 'http://localhost:8000'){
            return 'index';
        }elseif($url == 'https://www.it-hilbert.com'){
            return 'index';
        }
        $url = str_replace('http://localhost:8000', '', $url);
        $url = str_replace('https://www.it-hilbert.com', '', $url);
        return $url;
    }

    public static function getURLBevor(){
        $url = url()->previous() ?? 'N/A';
        if($url == 'http://localhost:8000'){
            return 'index';
        }elseif($url == 'https://www.it-hilbert.com'){
            return 'index';
        }
        $url = str_replace('http://localhost:8000', '', $url);
        $url = str_replace('https://www.it-hilbert.com', '', $url);
        return $url;
    }

    public static function getID(){
        $id = self::getIP();
        $id .= self::getPlatform();
        $id .= self::getAgent();
        $id .= self::getSpracheLong();

        if(strlen($id) > 255){
            $id = substr($id,0,254);
        }
        return $id;
    }



}
