<?php

namespace OceanWebTurk\Http;

use OceanWebTurk\Support\Traits\Macro;

class URL
{   
    use Macro;
    public static function host()
    {
        if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host     = $_SERVER['HTTP_X_FORWARDED_HOST'];
            $elements = explode(',', $host);
            $host     = trim(end($elements));
        } else {
            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : ''));
        }
        $host = trim($host);
        return $host;
    }
    public static function protocol()
    {
        return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || @$_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http');
    }
    public static function base()
    {
        return self::protocol()."://".self::host();
    }
}
