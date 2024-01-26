<?php 

namespace OceanWT\Support\Traits;

trait Macro
{
 /**
  * @var array
  */
 protected static $macros=[];

 /**
  * @param  string   $name    
  * @param  array|object|callable $callback
  */
 public static function macro(string $name,array|object|callable $macro)
 {
   self::$macros[$name]=$macro;
 }
 
 /**
  * @param  string  $name
  * @return boolean      
  */
 public static function hasMacro(string $name)
 {
  return isset(self::$macros[$name]);
 }
 
 /**
  * @return void
  */
 public static function flushMacros()
 {
   static::$macros = [];
 }
 
 /**
  * @param  string $method
  * @param  array  $params
  * @return mixed
  */
 public static function __callStatic(string $method,array $params)
 {
  return self::useClass($method,$params);
 }
 
 /**
  * @param  string $method
  * @param  array  $params
  * @return mixed
  */
 public function __call(string $method,array $params)
 {
  return self::useClass($method,$params);
 }
 
 /**
  * @param  string $method
  * @param  array  $params
  * @return mixed
  */
 public static function useClass(string $method,array $params)
 {
  $class=new self();
  if(static::hasMacro($method)){
   return self::getMacroTypeAction($method,$params);
  }elseif(method_exists($class,$method)){
   return call_user_func_array([$class,$method],[...$params]);
  }elseif(method_exists($class,'__callMacro')){
   return self::__callMacro($method,$params);
  }
 }

 /**
  * @param  string $method
  * @param  array  $params
  */
 private static function getMacroTypeAction(string $method,array $params)
 {
  $macro=self::$macros[$method];
  if(is_callable($macro)){
   return $macro(...$params);
  }elseif(is_array($macro)){
   $class=(new $macro[0]);
   $callback=$macro[1];
   return $class->$callback(...$params);
  }
 }
}
