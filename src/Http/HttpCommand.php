<?php

namespace OceanWT\Http;

use OceanWT\Command;

class HttpCommand extends Command
{
 /**
  * @param  array  $params
  */
 public function controller(array $params=[])
 {
  $file_sample=file_get_contents(__DIR__.'/Views/controller.sample');
  $class_name=ucfirst($params[1]);
  $content=str_replace(['{NAMESPACE}','{CLASS_NAME}'],
  [rtrim(GET_NAMESPACES['CONTROLLERS'],'\\'),$class_name],$file_sample);
  $file_name=GET_DIRS['CONTROLLERS'].$class_name.'.php';
  if(file_exists($file_name)){
   $message="
  File Exists : ".$file_name."
   ";
   $color="red";
  }else{
   file_put_contents($file_name,$content);
   $message="
  Controller file maked [".$file_name."]
  ";
  $color="green";
  }
  $this->write($message,$color);
 }
}
