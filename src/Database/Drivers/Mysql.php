<?php
namespace OceanWebTurk\Framework\Database\Drivers;

use Database\BaseDriver;

class Mysql extends BaseDriver
{   
  /** 
   * @param array $params
   * @return mixed
  */
  public function __construct(array $params = [])
  {
    parent::__construct('mysql:host='.$params['host'].';dbname='.$params['dbname'], $params['user'], $params['password']);
  }
  
  /**
   * @param string $sql
   * @param array $params
   */
  public function sql(string $sql,array $params = [])
  {
   $dbSql = $this->prepare($sql);
   $dbSql->execute($params);
  }
}
