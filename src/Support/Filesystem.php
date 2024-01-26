<?php 
namespace OceanWT\Support;
class Filesystem
{
 use Traits\Macro;

 /**
  * @param  string $file
  * @param  string $content
  */
 public static function createFile(string $file,string $content){
  file_put_contents($file,$content);
 }
}
