<?php
namespace OceanWebTurk\Framework\Http;

use OceanWebTurk\Framework\Support\Lang;
use OceanWebTurk\Framework\Debugbar\Debugbar;
use OceanWebTurk\Framework\Support\Traits\Macro;

class Route
{
 use Macro;

 /**
  * @var array
  */
 public static $configs = [];

 /**
  * @var array
  */
 public static $routes = [];
 
 /**
  * @var array
  */
 public static $options = [];
 
 /**
  * @var array
  */
 public $allowMethods = ['*','GET','POST','PUT','DELETE'];

 /**
  * @var array
  */
 public $patterns = [
  '{:all[0-9]?}' => '(.*?)',
  '{:id[0-9]?}' => '([0-9]+)',
  '{:locale[0-9]?}' => '([a-z0-9-_\/]+)',
  '{:url[0-9]?}' => '([a-z0-9-_\/]+)',
 ];

 public function __construct()
 {
  self::$configs = config("app")+config("router")+[
   'defaultMethod' => 'index',
   'defaultController' => 'HomeController',
   'defaultNamespace' => GET_NAMESPACES['CONTROLLERS'],
   'auto_view_load' => true,
   'auto_lang_alternate_tag' => true
  ];
 }

 /**
  * @param  string $path
  * @param  string|array|callable $callback
  * @param  array  $options
  * @return mixed
  */
 public static function get(string $path,$callback,array $options = [])
 {
  self::addMethod($path,$callback,"GET",$options);
  return new self();
 }
 
 public static function matchLang(array $langAndURIS,$callback,array $options = [])
 {
  $methods = isset($options['methods']) ? $options['methods'] : ["GET","POST"];
  foreach ($langAndURIS as$lang=>$path){  
    $as = isset($options['as']) ? $options['as'].'.'.$lang : $options['as'];
    self::addMethod($path,$callback,$methods,array_merge($options,['lang' => $lang,'asLang' => $as,'key' => md5(serialize($callback)),'langs' => $langAndURIS]));
  }
  return new self();
 }
 
 /**  
  * @param string $path
  * @param string|object|array $callback
  * @param string|array $methods
  * @param array $options
  * @return mixed
  */
 public static function addMethod(string $path,$callback,$methods,array $options = [])
 {
  $options = array_merge($options,self::$options);
  $host = isset($options['domain']) ? $options['domain'] : '{{CURRENT_DOMAIN}}';
  self::$routes[$host.(isset($options['prefix']) ? $options['prefix'] : '').$path] = [
   'methods' => $methods,
   'callback' => $callback,
   'options' => $options
  ];
  return new self();
 }
 
 /**
  * @param array $options
  * @param \Closure $function
  * @return mixed
  */
 public static function group(array $options,\Closure $function)
 {
  self::$options = $options;
  $function();
  self::$options = [];
  return new self();
 }

 /**
  * @param string $name
  * @param array $params
  * @return mixed
  */
 public static function url(string $name,array $params = [])
 {
  $defaultLang = (new Lang())->getLang('default');
  $route=array_key_last(array_filter(self::$routes,function($route)use($name,$defaultLang){
    if(isset($route['options']['asLang'])){
     return isset($route['options']['as']) && $route['options']['as'] === $name && 
     $route['options']['lang'] === $defaultLang;
    }
    return isset($route['options']['as']) && $route['options']['as'] === $name;
  }));

  return URL::protocol().'://'.str_replace(['{{CURRENT_DOMAIN}}','{:locale}'],
    [URL::host(),$defaultLang],
    (array_keys($params) ?  str_replace(array_map(fn($key) => '{:'.$key.'}' ,
    array_keys($params)),array_values($params),$route) : $route));
 }
 
 /**
  * @var string $path
  * @return string
  */
 public function regexChanger(string $path)
 {
  foreach($this->patterns as$key => $pattern){
    $path = preg_replace('#' . $key . '#', $pattern, $path);
  }
  return $path;
 }

 /**
  * @return mixed
  */
 public function run()
 {
  $url = Request::security(Request::getUrl(),true);
  $lang = new Lang();    
  $debugbar = new Debugbar();

  foreach(self::$routes as$path=>$props){
   if(str_starts_with($path,'{{CURRENT_DOMAIN}}')){
    $path = str_replace('{{CURRENT_DOMAIN}}','',$path);
   }
   $pattern = '#^'.$this->regexChanger($path).'$#';

   if(preg_match($pattern,$url,$params)){
    array_shift($params);
    $callback = $props['callback'];
    http_response_code(200);

    if(isset($props['options']['lang'])){
      $lang->setLang($props['options']['lang']);
    }

    if(isset($params[0]) && in_array($params[0],self::$configs['supported_langs'])){ 
      $lang->setLang($params[0]); 
    }
    header("Content-Language: ".$lang->getLang());
    
    $props['_url'] = $url;
    $props['lang'] = $lang;
    if(isset($props['options']['key'])){
      $props['_key']=$props['options']['key'];
    }
    
    if(is_callable($callback)){
      echo call_user_func_array($callback,[$params]);
    }elseif(is_array($callback)){
      $this->stringOrArrayCall($class,$callback,$params,$props);
    }elseif(is_string($callback)){
      $this->stringOrArrayCall(null,explode("::",$callback),$params,$props);
    }
   }
  }
 }
 
 /**
  * @param object|null $class
  * @param array $args
  * @param array $params
  * @param array $options
  * @return mixed
  */
 private function stringOrArrayCall($class = null,array $args = [],array $params = [],array $options = [])
 {
  $className = (isset($options['options']['namespace']) ? $options['options']['namespace'] : self::$configs['defaultNamespace']).$args[0];
  $class = new $className();
  $method = (isset($args[1]) ? $args[1] : self::$configs['defaultMethod']);

  if(!method_exists($class,$method)){ 
    http_response_code(500);
    throw new \Exception(sprintf(lang("system:method_not_found"),$className.'::'.$method));
  }

  if(class_exists(\SuperWeb::class)){ 
    $superweb = new \SuperWeb();
    if(lang($method)) $superweb->title(lang($method));
    if(lang($method.'_description')) $superweb->description(lang($method.'_description'));
    if(lang($method.'_keywords')) $superweb->keywords(lang($method.'_keywords'));

    if(isset(self::$configs['auto_lang_alternate_tag']) && self::$configs['auto_lang_alternate_tag']==true){
      $superweb->alternateLangTag(site_url(ltrim($options['_url'],'/')),'x-default');
      $superweb->alternateLangTag(site_url(ltrim($options['_url'],'/')),$options['lang']->getLang());

      $multiLangURI = ltrim(str_replace('{{CURRENT_DOMAIN}}','',array_key_last(array_filter(self::$routes,function($route)use($options){
        return isset($route['options']['key']) && $route['options']['key'] == $options['options']['key'];
      }))),'/');

    }
  }

  echo call_user_func_array([$class,$method],[$params]);
  $options['viewPath'] = isset($GLOBALS['_OCEANWEBTURK']['VIEWS']['argv']['path']) ? $GLOBALS['_OCEANWEBTURK']['VIEWS']['argv']['path'] : GET_DIRS['VIEWS'];
  $data = ['_params'=>$params];
  if(isset(Controller::$viewData)){
    $data = array_merge($data,Controller::$viewData);
  }

  $controllerControl = true;
  if(isset($class::$autoViewLoad)){
    $controllerControl = $class::$autoViewLoad;
  }

  if(isset(self::$configs['auto_view_load']) 
    && self::$configs['auto_view_load']==true && $controllerControl){
    return array_map(function($view)use($method,$params,$data,$options){
      if(str_starts_with($view,$options['viewPath'].$method)){
       echo view($method,$data);
      }
    },glob($options['viewPath'].'*'));
  }
 }
}
