<?php
namespace OceanWebTurk\Framework\Http;

use OceanWebTurk\Framework\Support\Lang;
use OceanWebTurk\Framework\Support\Traits\Macro;

class Controller
{   
  use Macro;
  
  /**
   * @var array
   */
  public static $viewData = [];
  
  /**
   * @var Lang
   */
  public $lang;

  /**
   * @var Request
   */
  public $request;
  
  /**
   * @var URL
   */
  public $url;

  public function __construct()
  {
   $this->url = new URL();
   $this->lang = new Lang();
   $this->request = new Request();
  }
  
  /**
   * @param array $data
   * @return mixed
   */
  public function data(array $data = [])
  {
   self::$viewData = $data;
   return new self();
  }
}
