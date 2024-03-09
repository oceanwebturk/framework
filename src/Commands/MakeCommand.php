<?php

namespace OceanWebTurk\Commands;

use OceanWebTurk\Command;

class MakeCommand extends Command
{

 /**
  * @param array $params
  */
 public function provider(array $params=[])
 {
  $file_sample=file_get_contents(GET_DIRS['SYSTEM'].'Views/provider.sample');
  $class_name=ucfirst($params[2]);
  $file_name=GET_DIRS['SERVICES'].$class_name.'.php';
  $content=str()->replace(['{NAMESPACE}','{CLASS_NAME}'],
  [rtrim(GET_NAMESPACES['SERVICES'],'\\'),$class_name],$file_sample);
  if(file_exists($file_name)){
   $message="
  File Exists : ".$file_name."
   ";
   $color="red";
  }else{
   file_put_contents($file_name,$content);
   $message="
   Provider Created [".$file_name."]
   ";
   $color="green";
  }
  $this->write($message,$color);
 }
}
