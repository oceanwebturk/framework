<?php 
use OceanWebTurk\Framework\Http\URL;
use OceanWebTurk\Framework\Http\Route;

if(!function_exists("site_url")){
  /**
   * @param string $url
   * @return string
   */
  function site_url(string $url = '')
  {
   return URL::base().'/'.$url;
  }    
}

if(!function_exists("route")){
   /**
    * @param string $name
    * @param array $params
    * @return string
    */
  function route(string $name,array $params = [])
  {
   return Route::url($name,$params);
  }    
}

if(!function_exists("url")){
   /**
    * @return mixed
    */
  function url()
  {
   return new URL();
  }    
}

if(!function_exists("redirect")){
   /**
    * @param string $url
    * @param int $time
    * @return mixed
    */
  function redirect(string $url,int $time = 0)
  {
   if($time==0){
    header("Location:".$url);
   }else{
    header("Refresh:".$time.";url=".$url);
   }
  }  
}

