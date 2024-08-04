<?php 
namespace OceanWebTurk\Framework;

use OceanWebTurk\Framework\Support\Traits\Macro;

class Import
{
 use Macro;

 /**
  * @var array
  */
 public static $paths = [];
 
 /**
  * @var array
  */
 public static $engines = [];
 
 /**
  * @param string $path
  * @param string $name
  * @param array $options
  * @return mixed
  */
 public static function addPath(string $path,string $name = 'default',array $options = [])
 {
  self::$paths[$name] = [
   'path' => $path,
   'options' => $options
  ];
  return new self;
 }
 
 /**
  * @param array $engines
  * @return mixed
  */
 public static function setEngines(array $engines = [])
 {
  self::$engines = array_merge(self::$engines,$engines);
  return new self;
 }

 /**
  * @param string $path
  * @param array $data
  * @param array $options = []
  * @return mixed
  */
 public static function view(string $path,array $data = [],array $options = [])
 {
  $engine = self::$engines[config("app")['template_engine']];
  $ex = explode(":",$path);

  $getPath = self::$paths[(isset($ex[1]) ? $ex[0] : 'default')];

  $options = array_merge($getPath['options'],['view' => $getPath['path']]);
  if(!isset($options['cache'])){
   $options = array_merge($options,self::$paths['default']['options']);
  }
  $options['cache'] = $options['cache'].md5('_'.$ex[0]); 

  $argv = ['name' => (isset($ex[1]) ? $ex[1] : $path),'path' => $getPath['path'],
  'options' => $options];
  $callback = $engine['callback'];
  $GLOBALS['_OCEANWEBTURK']['VIEWS'] = compact("argv","data","options");

  if(is_callable($callback)){
  	echo $callback($argv,$data,$options);
  }  
 }
}