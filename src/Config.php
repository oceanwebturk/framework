<?php
namespace OceanWebTurk\Framework;

use function file_exists;
use OceanWebTurk\Framework\Support\Traits\Macro;

class Config
{
 use Macro;
 /**
  * @var array
  */
 public static $paths = [];
 
 /**
  * @param string $path
  * @param string $name
  * @param array $options
  * @return mixed
  */
 public function addPath(string $path,string $name = 'default',array $options = [])
 {
  self::$paths[$name] = ['path'=>$path,'options' => $options];
 }

 public function get(string $name)
 {
  $ex = explode(":",$name);
  $path = isset($ex[1]) ? $ex[0] : 'default';
  $name = isset($ex[1]) ? $ex[1] : $name;
  $config = self::$paths[$path]['path'].$name;
  if(file_exists($config.'.php')){
    switch (gettype(include($config.'.php'))) {
      case 'array':
        return include($config.'.php');
      break;
      
      default:
        include($config.'.php');
      break;
    }
  }else{
   return [];
  }
 }
}