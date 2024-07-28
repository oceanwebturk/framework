<?php
namespace OceanWebTurk\Framework\Http;

use OceanWebTurk\Framework\Support\Traits\Macro;

class Controller
{   
  use Macro;
  
  /**
   * @var array
   */
  public static $viewData = [];

  public function data(array $data = [])
  {
   self::$viewData = $data;
   return new self();
  }
}
