<?php
namespace OceanWebTurk\Framework\Database;

use OceanWebTurk\Framework\Support\Traits\Macro;

class Migration
{  
  use Macro;

  /**
   * @var array
   */
  public $columns = [];

  /**
   * @var array
   */
  protected $config = [];

  /**
   * @var array
   */
  protected $primaryKeys = [];

  /**
   * @var array
   */
  protected $connection = [];

  public function __construct(array $config = [])
  {
   $this->config = $config;
   $this->connection = $GLOBALS['_OCEANWEBTURK']['DB']['CONNECTIONS'][$this->config[0]];
  }
  
  /**
   * @param array $columns
   * @return mixed
   */
  public function createTable(array $columns = [])
  {
    foreach ($columns as$name=>$options){
      $this->columns[]=' '.$name.' '.$this->typeAndValue($options);
    }
  }
  
  /**
   * @param string $name
   * @return mixed
   */
  public function primaryKey(string $name)
  {
   $this->primaryKeys[] = $name;
   return new self();
  }

  public function generateTable()
  {   
    $this->sql = 'CREATE TABLE IF NOT EXISTS `'.$this->connection['prefix'].$this->config[1].'` (
   ';
   $columns = [];
   $this->up();
   if(isset($this->primaryKeys)){
    foreach ($this->primaryKeys as$primaryKey){
      $columns[]='PRIMARY KEY('.$primaryKey.')';
    }
   }
   $this->sql.=implode(",",array_merge($this->columns,$columns)).');';
   return $this->sql;
  }
  
  /**
   * @param array $options
   * @return mixed
   */
  private function typeAndValue(array $options = [])
  {
   switch ($options['type']) {
    case 'int':
      return 'int('.(isset($options['value']) ? $options['value'] : '11').')'; 
    break;

    case 'string':
      return 'varchar('.(isset($options['value']) ? $options['value'] : '255').')'; 
    break;
   }
  }
}
