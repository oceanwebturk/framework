<?php
namespace OceanWebTurk\Framework\Http;

use OceanWebTurk\Framework\Support\Traits\Macro;

class Request
{   
    use Macro;

    /**
     * @var array
     */
    public $types=[
     'int' => [self::class,'type'],
     'email' => [self::class,'type'],
     'string' => [self::class,'type'],
     'required' => [self::class,'type'],
    ];

    /**
     * @return string
     */
    public static function method()
    {
     return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return boolean
     */
    public static function isGet()
    {
      return self::method()=='GET';
    }
    
    /**
     * @return boolean
     */
    public static function isPost()
    {
      return self::method()=='POST';
    }

    /**
     * @return string
     */
    public static function getUrl()
    {
     $dirname = dirname($_SERVER['SCRIPT_NAME']);
     $dirname = $dirname != '/' ? $dirname : '';
     $basename = basename($_SERVER['SCRIPT_NAME']);
     $path = $_SERVER['REQUEST_URI'];
     $position = strpos($path, '?');
     if ($position !== false) {
         $path = substr($path, 0, $position);
     }
     return str_replace([$dirname, $basename],'',$path);
    }

     /**
      * @param string|int $data
      * @param boolean $st
      * @return mixed
      */
    public static function security($data, $st = false)
    {
     if($st) {
      return trim(addslashes(htmlspecialchars(strip_tags(htmlentities($data)))));
     } else {
      return trim(addslashes(htmlspecialchars(htmlentities($data))));
     }
    }
    
    /**
     * @param string|int   $data
     * @param bool|boolean $st
     * @return mixed
     */
    public static function get($data,bool $st = false)
    {
     return self::security(@$_GET[$data],$st);
    }
    
    /**
     * @param string|int   $data
     * @param bool|boolean $st
     * @return mixed
     */
    public static function post($data,bool $st = false)
    {
     return self::security(@$_POST[$data],$st);
    }
    
    /**
     * @return mixed
     */
    public function parse()
    {
     $methods=array_map(function($validate){
      return explode("|",$validate);
     },array_values($this->controlData()));
     foreach($methods as$value){
      array_map(function($call){
       [$class,$method]=$this->types[$call];
       $class=new $class();
       if(method_exists($class,$method)){
        $class->$method("dasda",$call);
       }
      },$value);
     }
    }

    /**
     * @param  string|int|float|array $key
     * @param  string $type
     * @return mixed
     */
    public function type($key,string $type)
    {
     switch($type){
      case 'int':
       return is_int($key);
      break;

      case 'email':
       return filter_input($key,FILTER_VALIDATE_EMAIL) ? true : false;
      break;

      case 'string':
       return is_string($key);
      break;
     }
    }
  
  /**
   * @return mixed
   */
  public static function getIP()
  {
   return $_SERVER['REMOTE_ADDR'];
  }
}
