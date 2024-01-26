<?php

namespace OceanWT\Database;

use OceanWT\Command;

class DatabaseCommand extends Command
{
 /**
  * @param array $params
  */
 public function create(array $params=[])
 {
  $sql='CREATE DATABASE '.$params[2];
  DB::query($sql)->run();
 }
 /**
  * @param array $params
  */
 public function drop(array $params=[])
 {
  $sql='DROP DATABASE '.$params[2];
  DB::query($sql)->run();
 }

 /**
  * @return void
  */
 public function migrate()
 {
  $paths=Migration::getPaths();
  for($i=0;$i<count($paths);$i++){
   $path=$paths[$i];
   $openDir=opendir($path);
   while($dir=readdir($openDir)){
    if(is_file($path.$dir) && $dir!='.' && $dir!='..'){
     $class=include($path.$dir);
     if(!method_exists($class,'up')){
      throw new \Exception(sprintf(lang("system::method_not_found"),$path.$dir."::up()"));
     }
     $class->up();
     $this->write("\n  ".sprintf(lang("system::migrated_message"),$path.$dir)."\n ");
    }
   }
  }
 }

 /**
  * @return void
  */
 public function migrate_rollback() : void
 {
  $paths=Migration::getPaths();
  for($i=0;$i<count($paths);$i++){
   $path=$paths[$i];
   $openDir=opendir($path);
   while($dir=readdir($openDir)){
    if(is_file($path.$dir) && $dir!='.' && $dir!='..'){
     $class=include($path.$dir);
     if(!method_exists($class,'down')){
      throw new \Exception(sprintf(lang("system::method_not_found"),$path.$dir."::down()"));
     }
     $class->down();
    }
   }
  }
 }

 /**
  * @param  array  $params
  */
 public function migration(array $params=[])
 {
  $migration_name=$params[2];
  $migration_sample=__DIR__.'/samples/migration.sample';
  $migration_file=GET_DIRS['MIGRATIONS'].date("dmY_His")."_".$migration_name."_table.php";
 }
}
