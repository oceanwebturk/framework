<?php
namespace OceanWebTurk\Framework\Commands;

use OceanWebTurk\Framework\Support\Traits\Macro;

abstract class BaseCommand
{
 use Macro;
 /**
  * @var \OceanWebTurk\Framework\Application
  */
 public $app;
 
 /**
  * @var \OceanWebTurk\Framework\Console
  */
 public $console;
 
 /**
  * @var array
  */
 public $colors = [  
  'black' => '30',
  'red' => '31',
  'green' => '32',
  'yellow' => '33',
  'blue' => '34',
  'magenta' => '35',
  'cyan' => '36',
  'light_gray' => '37',
  'dark_gray' => '90',
  'light_red' => '91',
  'light_green' => '92',
  'light_yellow' => '93',
  'light_blue' => '94',
  'light_magenta' => '95',
  'light_cyan' => '96',
  'white' => '97',
 ];
 
 /**
  * @var array
  */
 public $bg_colors = [
    'blackbg'=>40, 
    'redbg'=>41, 
    'greenbg'=>42, 
    'yellowbg'=>44,
    'bluebg'=>44,
    'magentabg'=>45, 
    'cyanbg'=>46, 
    'lightgreybg'=>47
 ];
 
 /**
  * @var array
  */
 public $styles = [
    'bold'=>1,
    'italic'=>3, 
    'underline'=>4, 
    'strikethrough'=>9
 ];

 public function __construct($app,$console)
 {
  $this->app = $app;
  $this->console = $console;
 }
 
 /**
  * @param string $message
  * @param string $color
  * @param array $options
  * @return void
  */
 public function message(string $message,string $color = 'white',array $options = []) : void
 {
  $style = array_map(function($sty){
    if(isset($this->styles[$sty])){
        return $this->styles[$sty]; 
    }
  },$options);

  $bg_colors = array_map(function($bg_color){ 
    if(isset($this->bg_colors[$bg_color])){
        return $this->bg_colors[$bg_color]; 
    }
  },$options);

  $array =  $style + [$this->colors[$color]] + $bg_colors;
  echo ' '.$message;
 }
 
 /**
  * @param string $message
  * @param array $options
  * @return void
  */
 public function info(string $message,array $options = []) : void
 {
  $this->message("\n [INFO]","blue");
  $this->message($message,'white',$options);
 }

 /**
  * @param string $message
  * @param array $options
  * @return void
  */
 public function warn(string $message,array $options = []) : void
 {
  $this->message("\n [WARNING]","yellow");
  $this->message($message,'white',$options);
 }

 /**
  * @param string $message
  * @param array $options
  * @return void
  */
 public function error(string $message,array $options = []) : void
 {
  $this->message("\n [ERROR]","red");
  $this->message($message,'white',$options);
 }

 /**
  * @param string $message
  * @param array $options
  * @return void
  */
 public function success(string $message,array $options = []) : void
 {
  $this->message("\n [SUCCESS]","green");
  $this->message($message,'white',$options);
 }
 
 /**
  * @return void
  */
 public function br(): void
 {
  $this->message("\n");
 }
}