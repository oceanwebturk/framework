<?php 
namespace OceanWebTurk;

use OceanWebTurk\Support\Traits\Macro;

class Container
{
 use Macro;
 /**
  * @var int|null
  */
 protected static $startTime=null;
 
 /**
  * @var string
  */
 protected static $application = null,$instance = null;

 /**
  * @var array
  */
 protected static $bindings=[],$aliases=[],$instances=[];

 /**
  * @var array
  */
 protected static $appNamespaces=[
  __NAMESPACE__.'\Application\\',
 ];

 public function __construct()
 {
  self::$startTime=microtime(true);
 }

 /**
  * @param  string $aliases
  * @param  array  $class
  */
 public static function alias(string $alias,string|array $class)
 {
   self::$aliases[$alias]=$class;
 }
 
 /**
  * @param string|object  $abstract
  * @param string|callable|array $callback
  */
 public static function bind(string|object $abstract,string|callable|array $callback)
 {
  self::$bindings[$abstract]=compact('callback');
 }
 
 /**
  * @param  object $name  
  * @param  array  $params
  * @return mixed
  */
 public static function make(string|object $name,array $params=[])
 {
  if(isset(self::$bindings[$name])){
   $arr=self::$bindings[$name]['callback'];
   if(is_object($arr)){
    echo call_user_func_array($arr,$params);
   }
  }
 }

 /**
  * @param string $namespace
  */
 public static function addAppNamespace(string $namespace)
 {
  self::$appNamespaces[]=$namespace;
  return new self;
 }

 /**
  * @return array
  */
 public static function getAppNamespaces()
 {
  return self::$appNamespaces;
 }

 /**
  * @return object
  */
 public static function getInstance()
 {
  if(is_null(self::$instance)){
   return new static;
  }
  return new self::$instance();
 }

 /**
  * @param object|null $name
  */
 public static function setInstance(object $name=null)
 {
  self::$instance=$name;
 }

 /**
  * @param  string $abstract
  * @param  mixed  $instance
  * @return mixed
  */
 public function instance(string $abstract,mixed $instance)
 {
  self::$instances[$abstract]=$instance;
 }

 /**
  * @return false|object
  */
 public static function getApplication(...$params)
 {
  if(!is_null(self::$application)){
   foreach(self::getAppNamespaces()as$namespace){
    $class=$namespace.self::$application;
    if(class_exists($class)){
     return new $class(...$params);
    }else{
     return false;
    }
   }
  }
 }
}
