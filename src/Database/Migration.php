<?php
namespace OceanWT\Database;

use OceanWT\Http\Request;
use OceanWT\Support\Traits\Macro;

class Migration
{
 use TableCreator,Macro;

 /**
  * @var array
  */
 public static $paths=[
   GET_DIRS['MIGRATIONS'],
 ];

 /**
  * @var string
  */
 protected $table;

 /**
  * @param string $path
  */
 public static function addPath(string $path)
 {
  self::$paths[]=Request::security($path,true);
  return new self;
 }

 /**
  * @return array
  */
 public static function getPaths()
 {
  return self::$paths;
 }

 /**
  * @param  array|null $vars
  */
 public function create(array $vars=null)
 {
  $args=isset($vars['connection']) && isset(DB::$connections[$vars['connection']]) ? DB::$connections[$vars['connection']] : DB::$defaultConnect;
  $tableName=Request::security((isset($vars['prefix']) ? $vars['prefix'] : $args['prefix']).(isset($this->table) ? $this->table : $vars['table']),true);
  $sql="CREATE TABLE IF NOT EXISTS ".$tableName." (";
  $sql.=implode(",",array_merge($this->parseColumns(),$this->getAllKeys()));
  $sql.='
  ) ENGINE=INNODB DEFAULT CHARSET='.(isset($args['charset']) ? $args['charset'] : 'utf8mb4').' COLLATE '.(isset($args['collate']) ? $args['collate'] : 'utf8mb4_turkish_ci').';';
  $sql=trim($sql,',');
  $this->build($sql);
  $this->emptyVaribles();
 }

 /**
  * @return string
  */
 private function getAllKeys()
 {
  $sql=[];
   foreach(self::$keys['primaryKeys']as$primaryKey){
   $sql[]='
   PRIMARY KEY(`'.$primaryKey.'`)';
  }
  foreach(self::$keys['uniqueKeys']as$uniqueKey){
   $sql[]='
   UNIQUE KEY(`'.$uniqueKey.'`)';
  }
  return $sql;
 }

 /**
  * @return string|null
  */
 private function parseColumns()
 {
  $sql=[];
  foreach(self::$columns as$row=>$column){
   $nullbale=isset($column['nullable']) ? 'DEFAULT NULL' : 'NOT NULL';
   $sql[]='
   `'.$row.'` '.$column['type'].'('.$this->getTypeAction($column).') '.$nullbale;
  }
  return $sql;
 }

 /**
  * @param string $query
  */
 private function build(string $query)
 {
  DB::query($query)->run();
 }

 /**
  * @param array $args
  */
 private function getTypeAction(array $args)
 {
  $type=$args['type'];
  $arrs=[];
  switch($type){
   case 'enum':
   foreach($args['vars']as$enum){
    $arrs['enums'][]="'".$enum."'";
   }
   return implode(",",$arrs['enums']);
   break;

   default:
   return isset($args['lenght']) ? $args['lenght'] : 11;
   break;
  }
 }

 /**
  * @return void
  */
 private function emptyVaribles()
 {
  self::$keys['primaryKeys']=[];
  self::$keys['uniqueKeys']=[];
  self::$columns=[];
 }

 /**
  * @param array|null $vars
  */
 public function drop(array $vars=null)
 {
  $args=isset($vars['connection']) && isset(DB::$connections[$vars['connection']]) ? DB::$connections[$vars['connection']] : DB::$defaultConnect;
  $tableName=Request::security((isset($vars['prefix']) ? $vars['prefix'] : $args['prefix']).(isset($this->table) ? $this->table : $vars['table']),true);
  return ["tableName"=>$tableName,"sql"=>"DROP TABLE ".$tableName];
 }
}
