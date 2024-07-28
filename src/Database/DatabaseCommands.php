<?php 
namespace OceanWebTurk\Framework\Database;

use OceanWebTurk\Framework\Commands\BaseCommand;

class DatabaseCommands extends BaseCommand
{
 
 /**
  * @return mixed
  */
 public function migrate()
 {
  foreach (glob(GET_DIRS['MIGRATION'].'*')as$dir){
   	$ex = explode("_table_",str_replace([GET_DIRS['MIGRATION'],'.php'],'',$dir));
    $class = include($dir);
    $class = new $class($ex);
    $class->generateTable();
    $this->message(PHP_EOL.' Migrated '.$dir.PHP_EOL);
  }
 }
}