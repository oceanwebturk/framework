<?php

namespace OceanWebTurk;

class Output
{
 public static function write($text)
 {
  echo "<pre>";
  print_r($text);
  echo "</pre>";
 }
}
