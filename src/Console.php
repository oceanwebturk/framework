<?php

namespace OceanWT;

class Console
{
 use Support\Traits\Macro;
 /**
  * @var array
  */
 public static $commands=[];

 /**
  * @var string
  */
 public static $description="";

 /**
  * @param  string $desc
  */
 public static function description(string $desc)
 {
  self::$description=$desc;
  return new self;
 }

 /**
  * @param  string   $name
  * @param  string|array|callable $action
  */

 /**
  * @param  string   $name
  * @param  string|array|callable $action
  * @param  array    $options
  */
 public static function command(string $name,string|array|callable $action,array $options=[])
 {
  self::$commands[$name]=[
   'action'=>$action,
   'options'=>$options,
   'description'=>self::$description
  ];
  return new self;
 }

 /**
  * @param  array  $params
  */
 public static function run(array $params)
 {
  self::showHeader();
  $command=isset($params[1]) ? $params[1] : 'help';
  if(isset(self::$commands[$command])){
   $action=self::$commands[$command]['action'];
   if(is_string($action) && class_exists($action)){
    self::callTypeMethod([new $action(),'run'],$params);
   }elseif(is_callable($action)){
    self::callTypeMethod($action,$params);
   }elseif(is_array($action)){
    self::callTypeMethod([new $action[0](),$action[1]],$params);
   }
  }
 }

 /**
  * @param  string|array|object|callable $method
  * @param  array  $params
  */
 private static function callTypeMethod(string|array|object|callable $method,array $params)
 {
  echo call_user_func_array($method,[$params]);
 }

 /**
  * @return string|void
  */
 private static function showHeader()
 {
  Command::write(sprintf(
   '  OceanWebTurk v%s
  ',
   OCEANWT_VERSION
  ));
 }

 /**
  * @param  object $excep
  */
 public static function exceptionHandler(object $excep)
 {
  Command::write("\n ".$excep->getMessage()." \n ".lang("system::public")['line']." : ".$excep->getLine()."\n ".lang("system::public")['file']." : ".$excep->getFile()." \n");
 }

 public static function errorHandler(int $no,string $message,string $file,int $line)
 {
  Command::write("\n ".$message." \n ".lang("system::public")['line']." : ".$line."\n ".lang("system::public")['file']." : ".$file." \n");
 }
}
