<?php
namespace OceanWT\Config;
class Config
{
 public function getFile($file)
 {
  if(file_exists($file)){
   if(is_array(include($file))){
    $return=include($file);
   }else{
    include($file);
    $return=isset($$name) ? $$name : '';
   }
  }else{
   $return=[];
  }
  return $return;
 }
}
