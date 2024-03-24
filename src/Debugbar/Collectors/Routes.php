<?php
namespace OceanWebTurk\Debugbar\Collectors;

use OceanWebTurk\Debugbar\BaseCollector;

class Routes extends BaseCollector{

 /**
  * @return array
  */
 public function init()
 {
  return [
   'id' => 'routes',
   'name' => 'Routes',
   'web_uri' => 'routes',
  ];
 }

 /**
  * @return void|string
  */
 public function back()
 {

 }
}
