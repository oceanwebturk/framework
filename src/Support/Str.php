<?php
namespace OceanWebTurk\Support;

class Str
{
 public static $string="";

 public static function text($string="")
 {
  self::$string=$string;
  return new self;
 }

 public static function replace($search,$replace,$str="")
 {
  return str_replace($search,$replace,(isset($str) ? $str : self::$string));
 }
}
