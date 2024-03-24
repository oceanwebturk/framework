<?php 

namespace OceanWebTurk\Support\Traits;

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
 public static function macro(string $name,$macro)
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
  if(self::hasMacro($method)){
    return self::useClass($method,$params);
  }else{
   if(method_exists((new self()),$method)){
    return call_user_func_array([self::class, $method], $params);
   }
  }
 }
 
 /**
  * @param  string $method
  * @param  array  $params
  * @return mixed
  */
 public function __call(string $method,array $params)
 {
  if(self::hasMacro($method)){
    return self::useClass($method,$params);
  }else{
   if(method_exists($this,$method)){
    return call_user_func_array([$this, $method], $params);
   }
  }
 }
 
 /**
  * @param  string $method
  * @param  array  $params
  * @return mixed
  */
 public static function useClass(string $method,array $params)
 {
  return self::getMacroTypeAction($method,$params);
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
