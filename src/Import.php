<?php

namespace OceanWT;

class Import
{
 use Support\Traits\Macro;

 /**
  * @var array
  */
 public static $paths=[
  GET_DIRS["VIEWS"],
 ];

 /**
  * @var array
  */
 public static $shareds=[],$engines=[];

 public function __construct()
 {
  self::$engines=Config::get("view")->engines;
 }

 /**
  * @return array
  */
 public static function getEngines(): array
 {
  return self::$engines;
 }

 public static function  createEngine(string $name,array|object $action): Import
 {
  self::$engines[$name]=$action;
  return new self;
 }

 public static function share(array $arr)
 {
  self::$shareds=array_merge(self::$shareds,$arr);
  return new self;
 }

 /**
  * @param  string $file
  * @param  array  $data
  */
 public static function custom_view(string $file,array $data = [])
 {
     extract($data);
     include($file);
 }
 
 /**
  * @param  string $name
  * @param  array  $data
  */
 public static function view(string $name,array $data = [])
 {
   $engine=OceanWT::$configs['templateEngine'];
   if(strpos($name,"::")){
    $ex=explode("::",$name);
    if($configs=self::$paths[$ex[0]]){
     $engine=isset(self::$engines[$configs['templateEngine']]) ? self::$engines[$configs['templateEngine']] : [TemplateEngine::class,'render'];
     OceanWT::$defines['VIEWS']=$configs['path'];
     $name=$ex[1];
    }
   }
   $data=array_merge($data,self::$shareds);
   if(is_array($engine)){
    echo @call_user_func_array([$engine[0],$engine[1]],[$name,$data]);
   }elseif(is_callable($engine)){
    echo call_user_func_array($engine,[$name,$data]);
   }
 }
}
