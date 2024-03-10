<?php

namespace OceanWebTurk;

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
  self::$engines=(Array) Config::get("owt-framework-etc::templateEngines");
 }

 /**
  * @return array
  */
 public static function getEngines(): array
 {
  return self::$engines;
 }

 /**
  * @param  string $name
  * @param  object $action
  */
 public static function  createEngine(string $name,$action): Import
 {
  self::$engines[$name]=[
   'action' => $action
  ];
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
   if(strpos($name,"::")){
    $ex=explode("::",$name);
    if($configs=self::$paths[$ex[0]]){
     $engine=self::$engines[$configs['templateEngine']];
    }
   }else{
    $engine=self::$engines[OceanWebTurk::$configs['templateEngine']];
   }
   $packages=(new PackageManifest())->getJsonManifest();
   if(isset($engine['package']) && !in_array($engine['package'],$packages)){
    throw new \Exception(sprintf(lang("system::not_found"),$engine['package']), 1);
   }
   $args=[
    'cachePath' => isset($configs['cachePath']) ? $configs['cachePath'] : GET_DIRS['CACHES'],
    'viewPath' => isset($configs['path']) ? $configs['path'] : GET_DIRS['VIEWS'],
    'name' => isset($ex[1]) ? $ex[1] : $name,
   ];
   $data=array_merge($data,self::$shareds);
   return self::callTypeTemplateEngine($engine,$args,$data);
 }

 public static function callTypeTemplateEngine($engine,$args,$data)
 {
  $engine=$engine['action'];
  if(is_array($engine)){
   echo @call_user_func_array([$engine[0],$engine[1]],[$args,$data]);
  }elseif(is_string($engine)){
   echo call_user_func_array(self::$engines[$engine]['action'],[$args,$data]);
  }elseif(is_callable($engine)){
   echo call_user_func_array($engine,[$args,$data]);
  }
 }

 /**
  * @param  array  $args
  * @param  array  $data
  */
 public static function render(array $args=[],array $data = [])
 {
  extract($data);
  include($args['viewPath'].$args['name'].".php");
 }
}
