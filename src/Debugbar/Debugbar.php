<?php
namespace OceanWebTurk\Framework\Debugbar;

class Debugbar
{
  /**
   * @return mixed
   */
  public function header()
  {
    return view("system:debugbar.h",['debugbar' => $this->init()]);
  }
  
  /**
   * @return mixed
   */
  public function body()
  {
    return view("system:debugbar",['debugbar' => $this->init()]);
  }
  
  /**
   * @return mixed
   */
  private function init()
  {
   $file = GET_DIRS['CONFIGS'].'debugbar.json';
   return [
    'container' => [
      'height' => '50px',
      'bg_color' => 'black',
      'text_color' => 'white',
    ]
   ];
  }
}