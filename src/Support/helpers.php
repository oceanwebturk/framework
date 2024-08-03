<?php

use OceanWebTurk\Framework\Support\Lang;
use OceanWebTurk\Framework\Support\Security;

if(!function_exists("lang")){
  /**
   * @param string $name
   * @return mixed
   */
  function lang(string $name)
  {
   return (new Lang())->get($name);
  }
}

if(!function_exists("security")){
  /**
   * @return mixed
   */
  function security()
  {
   return new Security();
  }
}

if(!function_exists("csrf_input")){
  /**
   * @return string
   */
  function csrf_input()
  {
   return security()->csrfInput();
  }    
}


if(!function_exists("dd")){
  /**
   * @param mixed $data
   * @return mixed
   * */
  function dd($data){
   echo '<div id="varDumperContainer" class="var-dumper-container">
   <p>Type: <b class="data-type">'.gettype($data).'</b></p><pre>';
   print_r($data);
   echo '</pre></div>';
  }
}