<?php

namespace OceanWebTurk\Http;

use OceanWebTurk\Config;
use OceanWebTurk\Support\Traits\Macro;

class Route
{ 
    use Macro,ManagerDomain;

    /**
     * @var array
     */
    public static $routes = [],$configs = [],$groupOptions = [];
    
    /**
     * @var array
     */
    public static $filters=[];

    /**
     * @var string|object
     */
    public static $prefix,$controller,$domain;
    
    /**
     * @var string|array
     */
    public static $method;

    /**
     * @var array
     */
    public static $patterns = [
     '{:id[0-9]?}' => '([0-9]+)',
     '{:url[0-9]?}' => '([a-z]+)',
    ];

    public function __construct()
    {
     self::$configs=Config::default(__NAMESPACE__.'\DefaultRoute')->get("routing");
     self::$filters=array_merge((Array) Config::get("owt-framework-etc::filters"),Config::get("filters")->aliases);
    }

    /**
     * @param  string $uri
     * @param    $action
     * @return Route
     */
    public static function get(string $uri, $action = null,array $options=[]): Route
    {
     return self::match("GET", $uri, $action,$options);
    }

    /**
     * @param  string $uri
     * @param    $action
     * @return Route
     */
    public static function post(string $uri, $action = null,array $options=[]): Route
    {
     return self::match("POST", $uri, $action,$options);
    }

    /**
     * @param  string $domain
     * @return Route
     */
    public static function domain(string $domain): Route
    {
     self::$domain=$domain;
     return new self();
    }

    /**
     * @param string $prefix
     * @return Route
     */
    public static function prefix(string $prefix): Route
    {
     self::$prefix.=$prefix;
     return new self;
    }

    /**
     * @param  array    $options
     * @param  \Closure $group
     * @return Route
     */
    public static function group(array $options,\Closure $group): Route
    {
     self::$groupOptions=$options;
     $group();
     self::$prefix='';
     self::$groupOptions=[];
     return new self();
    }

    /**
     * @param  string $pattern
     * @return string
     */
    protected static function createPattern($pattern): string
    {
     foreach(self::$patterns as$key=>$value){
      $path=preg_replace('#^'.$key.'$#',$value,$pattern);
     }
     return $path;
    }

    /**
     * @param string|array $method
     * @param string $uri
     * @param array|string|null|callable $action
     * @param array $options
     * @return Route
     */
    public static function match(string $method, string $uri, $action,array $options=[])
    {
     $controller=self::$controller;
     self::$method=$method;
     $options=array_merge(self::$groupOptions,$options);
     self::$routes[isset($options['domain']) ? $options['domain'] : self::$domain][((isset($options['prefix']) ? $options['prefix'] : (isset(self::$prefix) ? self::$prefix : '')).$uri)] = [
      'action' => (in_array(gettype($controller),["object","string"],true) ? $controller."::".$action : $action),
      'options' => $options,
      'methods' => $method,
      'domain' => isset($options['domain']) ? $options['domain'] : self::$domain,
     ];
     return new self;
    }
   
   /**
    * @param  string|object|array $controller
    * @return Route
    */
   public static function controller($controller): Route
   {
    self::$controller=$controller;
    return new self;
   }

   /**
    * @param  string $mode
    * @return Route
    */
   public static function mode($mode): Route
   {
    self::$routes[array_key_last(self::$routes)]['mode']=$mode;
    return new self;
   }

   /**
    * @param  string $name
    * @return Route
    */
   public static function name(string $name): Route
   {
    self::$routes[array_key_last(self::$routes)]['options']['as']=$name;
    return new self;
   }

   /**
    * @param  string $name
    * @param  string $pattern
    */
   public static function where(string $name,string $pattern)
   {
    self::$patterns['{:'.$name.'[0-9]?}']='(['.$pattern.']+)';
    return new self;
   }
   
   /**
    * @param string $name
    * @param array $params
    */
   public static function url(string $name,array $params=[])
   {
    $route=array_key_last(array_filter(self::$routes,function($route)use($name){
     return isset($route['options']['as']) && $route['options']['as'] === $name;
    }));
    return public_url().(!array_keys($params) ? $route : str_replace(array_map(function($key){
     return '{:'.$key.'}';
    },array_keys($params)),array_values($params),$route));
   }

   public function run()
   {
    $url = str_replace(array_keys(self::$configs->uriReplaceCharacters),array_values(self::$configs->uriReplaceCharacters),urldecode(Request::security(Request::getUrl(),true)));
    $domain=isset(self::$routes[URL::host()]) ? URL::host() : '';
    if(isset(self::$sites[URL::host()])){
     $this->customSite(URL::host());
    }elseif(isset(self::$routes[$domain])){
     $this->routeExec($url,$domain);
    }
   }

   /**
    * @param string|array|callable $action
    * @param array $params
    * @param array $props
    */
   private function setActionTypeController(string|array|callable $action,array $params,array $props)
   {
    if(is_callable($action)) {
      echo call_user_func_array($action, $params);
    } elseif(is_array($action)) {
      $this->arrayOrStringParseRoute($props,$action,$params);
    } elseif(is_string($action)) {
      $this->arrayOrStringParseRoute($props,$action,$params);
    }
   }

   /**
    * @param  array  $props
    * @param  string|array  $action
    * @param  array  $params
    */
   private function arrayOrStringParseRoute(array $props, $action,array $params)
   {
    $data=is_string($action) ? explode("::", $action) : $action;
    $namespace = $this->setControllerAndNamespace($action,$props['options'])['namespace'];
    $className = $this->setControllerAndNamespace($action,$props['options'])['class'];
    if(!class_exists($className)){
     throw new \Exception(sprintf(lang("system::controller_not_found"),$className), 1);
    }
    $class=new $className();
    $method = isset($data[1]) ? $data[1] : self::$configs->defaultFunction;
    if(method_exists($class,$method)){
     echo call_user_func_array([$class,$method], $params);
    }else{
     throw new \Exception(sprintf(lang("system::method_not_found"),$namespace.$className.'::'.$method), 1);
    }
   }

   /**
    * @param string|array $action
    * @param array $props
    * @return array
    */
   private function setControllerAndNamespace($action,array $props): array
   {
    $data=is_string($action) ? explode("::", $action) : $action;
    $className = $data[0];
    $namespace = (isset($props['namespace']) ? $props['namespace'] : self::$configs->defaultNamespace);
    return ['namespace'=>$namespace,'class'=>$namespace.$className];
   }

   /**
    * @param object $class
    */
   private function filter_get(object $class)
   {
    if(method_exists($class,'handle')){
     return $class->handle();
    }
   }

   /**
    * @param  string $url
    * @param  string $domain
    */
   private function routeExec(string $url,string $domain)
   {
    foreach(self::$routes[$domain]as$path => $props) {
     foreach(self::$patterns as$key=>$value){
      $path=preg_replace('#^'.$key.'$#',$value,$path);
     }
     $pattern = '#^'.$path.'$#';
     if(preg_match($pattern,$url,$params) && in_array($_SERVER['REQUEST_METHOD'],(is_array($props['methods']) ? $props['methods'] : [$props['methods']]))) {
      http_response_code(200);
      array_shift($params);
      $action = $props['action'];
      if(isset($props['options']['minify'])){
       $GLOBALS['_OCEANWEBTURK']['MINIFY'] = $props['options']['minify'];
      }
      $this->filterRun($props);
      $this->setActionTypeController($action,$params,$props);
     }else{
      http_response_code(404);
     }
    }
   }

   /**
    * @param  array  $props
    */
   private function filterRun(array $props = [])
   {
     if(isset($props['options']['filters'])){
      $filters=$props['options']['filters'];
      array_map(function($filter){
       if(class_exists(self::$filters[$filter])){
        $class=self::$filters[$filter];
        $class=new $class();
        $this->filter_get($class);
       }
     },$filters);
    }
   }
}
