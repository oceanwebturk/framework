<?php
namespace OceanWebTurk\Config;
class Json
{
 public $fileExtension=".json";
 public function getFile($file)
 {
  if(file_exists($file)){
   $return=json_decode(file_get_contents($file),true);
  }else{
   $return=false;
  }
  return $return;
 }
}
