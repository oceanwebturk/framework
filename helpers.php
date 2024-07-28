<?php 

use OceanWebTurk\Framework\Application;
use OceanWebTurk\Framework\Config;

define("OCEANWEBTURK_VERSION","1.0");

if(!function_exists("is_cli")){
    /**
     * @return boolean
     */
   function is_cli()
   {
    if(in_array(PHP_SAPI,['cli','php-cli'])){
     return true;
    }
    return false;
   }
}

if(!function_exists("app")){
   
    /**
     * @param mixed $name
     * @return mixed
     */
   function app($name = null)
   {
    return (new Application())->resolve($name);
   }
}

if(!function_exists("config")){
  /**
   * @param string $name
   * @return mixed
   */
  function config(string $name)
  {
   return (new Config())->get($name);
  }
}