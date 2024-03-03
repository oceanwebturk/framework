<?php

namespace OceanWebTurk\Support;

class Lang
{

 /**
  * @var array
  */
 public static $paths=[
  0 => ['path' => GET_DIRS["LANGS"]],
  'system' => ['path' => GET_DIRS['SYSTEM'].'Langs/','lang' => 'en'],
 ];

 /**
  * @var string|object|null|boolean
  */
 private static $default=false;

 /**
  * @param  string|object $default
  */
 public static function default($default)
 {
  self::$default=$default;
  return new self;
 }

 public static function set(string $name,string $path,string $lang='en')
 {
  self::$paths[$name]=[
    'path' => $path,
    'lang' => $lang
  ];
  return new self;
 }

 /**
  * @param  string $name
  */
 public static function get(string $name)
 {
  $args=self::setData($name);
  $path=$args[0];
  $files=[
   'json_lang_code' => (isset(self::$paths[$path]) ? self::$paths[$path]['path'].self::$paths[$path]['lang'] : self::$paths[0]['path'].self::$paths[0]['lang']).".json"
  ];

  $return=self::jsonDataGet($files['json_lang_code'],$name);

  if($default=self::getDefault()){
   $return=$return + $default;
  }

  return $return;
 }

 /**
  * @param string $name
  * @return string|array
  */
 private static function setData(string $name)
 {
   if(strpos($name,'::')){
    return explode("::",$name);
   }else{
    return [self::$paths[0],$name];
   }
 }

 /**
  * @param string $file_name
  * @param string $key
  * @return string|array
  */
 private static function jsonDataGet(string $file_name,string $key)
 {
   $file=json_decode(file_get_contents($file_name),true);
   $key=self::setData($key)[1];
   if(isset($file[$key])){
    return $file[$key];
   }elseif(isset($file->$key)){
    return $file->$key;
   }else{
    return '';
   }
 }

 /**
  * @return object
  */
 private static function getDefault(){
  $default=self::$default;
  self::$default=NULL;
  if(is_string($default)){
   return get_class_vars($default);
  }elseif(is_object($default)){
   return get_object_vars($default);
  }else{
   return false;
  }
 }

 /**
  * @param string $phpFile
  * @return boolean|array
  */
 private static function phpFile(string $phpFile)
 {
  $file=include($phpFile);
  if(is_array($file)){
   return $file;
  }else{
   return '';
  }
 }
}
