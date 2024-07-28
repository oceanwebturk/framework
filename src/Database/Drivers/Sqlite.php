<?php
namespace OceanWebTurk\Framework\Database\Drivers;

use SQLite3;

class Sqlite extends BaseDriver
{   
  /** 
   * @param array $params
   * @return mixed
  */
  public function __construct(array $params = [])
  {
    try {
      $this->connect = ! empty($params['password'])
      ? new SQLite3($params['database'], SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $params['password']) : new SQLite3($paramsconfig['database']);
    } catch (Exception $e) {
      throw new \Exception($e);
    }
  }
  
  /**
   * @param string $sql
   * @param array $params
   * @return mixed
   */
  public function sql(string $sql,array $params = [])
  {
   if(empty($sql)){
    return false;
   }
   return $this->connect->exec($sql);
  }
}
