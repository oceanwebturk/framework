<?php

namespace OceanWebTurk\Support;

class Lang
{

 /**
  * @var array
  */
 public static $paths=[
  GET_DIRS["LANGS"],
  'system'=>GET_DIRS['SYSTEM'].'Langs/',
 ];

 /**
  * @var string
  */
 public static $appLang,$sysLang="en";

 /**
  * @var string|object|null|boolean
  */
 private static $default=false;

 /**
  * @param  string|object $default
  */
 public static function default(string|object $default)
 {
  self::$default=$default;
  return new self;
 }

 /**
  * @param string $lang
  */
 public static function setAppLang(string $lang)
 {
  self::$appLang=$lang;
 }

 /**
  * @param  string $name
  */
 public static function get(string $name)
 {
   $args=self::setData($name);
   $path=$args[0];
   $phpFiles=[
    'app'=>(isset(self::$paths[$path]) ? self::$paths[$path] : $path).self::$appLang.'/'.$args[1].".php",
    'sys'=>(isset(self::$paths[$path]) ? self::$paths[$path] : $path).self::$sysLang.'/'.$args[1].".php"
   ];

   $jsonFiles=[
    'app'=>(isset(self::$paths[$path]) ? self::$paths[$path] : $path).self::$appLang.'.json',
    'sys'=>(isset(self::$paths[$path]) ? self::$paths[$path] : $path).self::$sysLang.'.json',
   ];

   if(!str_starts_with("http://",$phpFiles['app']) || !str_starts_with("https://",$phpFiles['app']) || !str_starts_with("http://",$phpFiles['sys']) || !str_starts_with("https://",$phpFiles['sys'])){
    if(file_exists($phpFiles['app'])){
     $return=self::phpFile($phpFiles['app']);
    }elseif(file_exists($phpFiles['sys'])){
     $return=self::phpFile($phpFiles['sys']);
    }
   }

   if(file_exists($jsonFiles['app'])){
    $return=self::jsonDataGet($jsonFiles['app'],$name);
   }elseif(file_exists($jsonFiles['sys'])){
    $return=self::jsonDataGet($jsonFiles['sys'],$name);
   }

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
