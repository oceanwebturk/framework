<?php 
namespace OceanWebTurk\Framework\Database\Drivers;

abstract class BaseDriver
{
 /**
  * @var mixed
  */
 protected $connect,$config;
 
 /**
  * @param int $start = null
  * @param int $limit = 0
  * @return DB
  * 
  * @codeCoverageIgnore
  */
 public function limit($start = NULL,$limit = 0)
 {
  return ' LIMIT '. ( ! empty($limit) ? $limit . ' OFFSET ' . $start. ' ' : $start );
 }
}