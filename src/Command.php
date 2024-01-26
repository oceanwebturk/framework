<?php

namespace OceanWT;

class Command
{
  use Support\Traits\Macro;
  /**
   * @param  string $text
   */
  public static function write(string $text)
  {
   echo $text;
  }
}
