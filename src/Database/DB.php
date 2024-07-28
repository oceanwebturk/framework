<?php
namespace OceanWebTurk\Framework\Database;

use OceanWebTurk\Framework\Support\Traits\Macro;

#[\AllowDynamicProperties]

class DB
{
  use Macro;

  /**
   * @var string
   */
  public $table;
  
  /** 
   * @var string
   */
  public $stringQuery = ""; 

  /**
   * @var string
   */
  public $select = "*";

  /**
   * @var array
   */
  public $connections = [];
  
  /**
   * @var array
   */
  public $defaultConnection = [];
  
  /** 
   * @return mixed
   */
  public function init()
  {
    $this->defaultConnection = $this->connections['default']; 
    $GLOBALS['_OCEANWEBTURK']['DB'] = [
      'DRIVERS' => $this->drivers,
      'CONNECTIONS' => $this->connections,
    ];
  }

  /** 
   * @return mixed
   */
  public function addConnection(array $params = [],string $name = 'default')
  {
    $this->connections[$name] = $params;
    return new $this;
  }
  
  /** 
   * @param string $name
   * @return mixed
   */
  public function connect(string $name = 'default')
  {
   if(isset($this->connections[$name])){
    $this->defaultConnection = $this->connections[$name]; 
   }
   return new self();
  }
  
  /**
   * @param string $name
   * @param mixed $class
   * @return \OceanWebTurk\Framework\Database\DB
   */
  public function addDriver(string $name,mixed $class)
  {
   $this->drivers[$name] = $class;
   return new self();
  }
  
  /**
   * @return mixed
   */
  public function getDriver()
  {
    $driver = __NAMESPACE__.'\\Drivers\\'.ucfirst($this->defaultConnection['driver']);
    if(class_exists($driver)){
     return new $driver($this->defaultConnection);
    }
  }
  
  /**
   * @param array $connection
   * @return void
   */
  public function setDefaultConnection($connection)
  {
   $this->defaultConnection = $connection;
  }
  
  /**
   * @param array $connections
   * @return void 
   */
  public function setConnections(array $connections)
  {
   $this->connections = $connections;
  }

  /**
   * @param string $var 
   * @return mixed
   **/
  public function table(string $table)
  {
   $this->table = $this->defaultConnection['prefix'].$table;
   return $this;
  }
  
  /**
   * @return mixed
   */
  public function get()
  {
    $this->stringQuery = "SELECT ".$this->select." FROM ".$this->table;
    return $this->getDriver()->sql($this->stringQuery);
  }

  public function __destruct()
  {
    if(method_exists(__NAMESPACE__.'\\Drivers\\'.ucfirst($this->defaultConnection['driver']), 'close')){
      $this->getDriver()->close();
    }
  }
}