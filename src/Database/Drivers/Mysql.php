<?php 

namespace OceanWT\Database\Drivers;

use PDO;
use OceanWT\Support\Traits\Macro;

class Mysql extends PDO
{
 use Macro;
 /**
  * @var \PDO
  */
 public $db;

 /**
  * @param array $data
  */
 public function __construct(array $data=[])
 {
  try {
    $options=array_merge($data['options'],[
    PDO::ATTR_PERSISTENT=>true,
    ]);
    $this->db=parent::__construct("mysql:host=".$data["host"].";".(isset($data['port']) ? 'port='.$data['port'].';' : '')."dbname=".$data['database'].";charset=".(isset($data['charset']) ? $data['charset'] : 'utf8').';',$data['user'],$data['password'],$options);
  } catch (PDOException $e) {
              
  }
 }

 /**
  * @param string $sql
  * @param array  $arr
  */
 public function sql(string $sql,array $arr=[])
 {
  $query=$this->prepare($sql);
  return $query->execute($arr);
 }
 
 /**
  * @param string $sql
  */
 public function get(string $sql)
 {
  return $this->sql($sql)->fetchAll();
 }

 /**
  * @return void
  */
 public function close(): void
 {
  $this->db=null;
 }
}
