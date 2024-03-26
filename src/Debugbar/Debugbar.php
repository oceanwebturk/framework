<?php

namespace OceanWebTurk\Debugbar;

use OceanWebTurk\Container;
use OceanWebTurk\Http\Route;
use OceanWebTurk\Support\Traits\Macro;

class Debugbar
{
 use Macro;

 /**
  * @var Container
  */
 public ?Container $container;

 /**
  * @var array
  */
 public static $collectorClasses=[
  \OceanWebTurk\Debugbar\Collectors\Routes::class
 ];

 /**
  * @var BaseCollector[]
  */
 public static $collectors=[];

 public function __construct()
 {
  $this->container=new Container();
 }

 /**
  * @return void
  */
 public function boot()
 {
  foreach(self::$collectorClasses as$collector){
   if(class_exists($collector)){
    self::$collectors[]=new $collector();
   }
  }
 }

 /**
  * @param string|object $class
  * @return Debugbar
  */
 public static function addCollect($class): Debugbar
 {
   self::$collectors[]=(is_object($class) ? $class : new $class());
   return new self;
 }

 /**
  * @return void|string
  */
 public static function run()
 {
  $data=[
   'totalMemory' => number_format(memory_get_peak_usage() / 1024 / 1024, 3),
   'version' => OCEANWEBTURK_VERSION,
   'collectors' => []
  ];
  foreach (self::$collectors as$collector){
   $data['collectors'][]=$collector->getDataArray();
  }
 }
}
