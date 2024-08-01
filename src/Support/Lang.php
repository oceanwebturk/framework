<?php
namespace OceanWebTurk\Framework\Support;

use function file_exists;
use OceanWebTurk\Framework\Support\Traits\Macro;

class Lang
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
 
 /**
  * @param string $name
  * @return string
  */
 public function getPath(string $name)
 {
  return self::$paths[$name];
 }
 
 /**
  * @param string $name
  * @return mixed
  */
 public function getLang(string $name = 'default')
 {
  return self::$paths[$name]['options']['lang'];
 }
 
 /**
  * @param string $lang
  * @param string $name
  * @return mixed
  */
 public static function setLang(string $lang,string $name = 'default')
 {
  return self::$paths[$name]['options']['lang'] = $lang;
 }
 
 /**
  * @param string $name
  * @return mixed
  */
 public function get(string $name)
 {
  $ex = explode(":",$name);
  $path = isset($ex[1]) ? $ex[0] : 'default';
  $name = isset($ex[1]) ? $ex[1] : $name;
  $lang = self::$paths[$path]['path'].self::$paths[$path]['options']['lang'];
  if(file_exists($lang.'.json')){
    return json_decode(file_get_contents($lang.'.json'),true)[$name];
  }
 }
}