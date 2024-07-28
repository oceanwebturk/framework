<?php
namespace OceanWebTurk\Framework\Database;

use OceanWebTurk\Framework\Support\Traits\Macro;

abstract class Model
{
  use Macro;
  
  /**
   * @var \OceanWebTurk\Framework\Database\DB
   */
  protected $db;

  /**
   * @var string
   */  
  protected $table;
  
  /** 
   * @var string
   */
  protected $sql = ""; 

  /**
   * @var string
   */
  protected $select = "*";

  /**
   * @var string
   */
  protected $connection;

  public function __construct()
  {
   $this->db = new DB();
  }
}