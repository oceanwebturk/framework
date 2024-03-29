<?php

namespace OceanWebTurk;

class Command
{
  use Support\Traits\Macro;
  
  public static $fgColors=[
  'red' => '31m',
  'green' =>'32m',
  'yellow' => '33m',
  'blue' => '34m',
  'purple' => '35m',
  'skyblue' => '36m',
  'white' => '37m',
  ];

  /**
   * @param  string $text
   */
  public static function write(string $text,string $color="red")
  {
   $left = "\x1b[".self::$fgColors[$color];
   echo " ".$text;
  }
}
