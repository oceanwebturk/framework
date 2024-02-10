<?php

namespace OceanWebTurk\Database;

use OceanWebTurk\Http\Request;

trait TableCreator
{

 /**
  * @var array
  */
 private static $columns=[];

 /**
  * @var array
  */
 private static $keys=[
  'primaryKeys'=>[],
  'uniqueKeys'=>[]
 ];

 /**
  * @param string $primaryKey
  */
 public function primaryKey(string $primaryKey)
 {
  self::$keys['primaryKeys'][]=Request::security($primaryKey,true);
  return new self;
 }

 /**
  * @param string $uniqueKey
  */
 public function uniqueKey(string $uniqueKey)
 {
  self::$keys['uniqueKeys'][]=Request::security($uniqueKey,true);
  return new self;
 }

 /**
  * @param string $name
  * @param array  $options
  */
 public function addColumn(string $name,array $options=[])
 {
  self::$columns[$name]=$options;
  return new self;
 }

 /**
  * @param string $name
  */
 public function string(string $name)
 {
  return $this->addColumn($name,[
   'type'=>'varchar'
  ]);
 }

 /**
  * @param  string $name
  */
 public function int(string $name)
 {
  return $this->addColumn($name,[
   'type'=>'int'
  ]);
 }

 /**
  * @param int $num
  */
 public function length(int $num)
 {
  self::$columns[array_key_last(self::$columns)]['length']=$num;
  return new self;
 }

 /**
  * @param  bool|boolean $status
  */
 public function nullable(bool $status=true)
 {
  self::$columns[array_key_last(self::$columns)]['nullable']=$status;
  return new self;
 }

 public function enum(string $columnName,array $vars=[])
 {
  return $this->addColumn($columnName,[
   'type'=>'enum',
   'vars'=>$vars
  ]);
 }
}
