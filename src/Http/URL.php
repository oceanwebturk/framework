<?php
namespace OceanWebTurk\Framework\Http;

use OceanWebTurk\Framework\Support\Traits\Macro;

class URL
{   
    use Macro;

    /**
     * @return string
     */
    public static function host()
    {
     if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
      $host     = $_SERVER['HTTP_X_FORWARDED_HOST'];
      $elements = explode(',', $host);
      $host     = trim(end($elements));
     } else {
      $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : ''));
     }
     return trim($host);
    }

    /**
     * @return string
     */
    public static function protocol()
    {
     return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http');
    }
    
    /**
     * @return mixed
     */
    public static function canonical()
    {
     if(isset($_SERVER['REQUEST_URI'])){
      return rtrim(site_url(),'/').rtrim(str_replace((isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''),'',$_SERVER['REQUEST_URI']),'/').'/';
     }
    }

    /**
     * @return string
     */
    public static function base()
    {
     return self::protocol()."://".self::host();
    }
}