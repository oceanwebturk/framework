<?php

use OceanWT\Http\URL;
use OceanWT\Http\Route;

if(!function_exists("base_url")){
 function base_url($url = null){
  return URL::base().$url;
 }
}
if(!function_exists("site_url")){
 function site_url($url = null){
    return URL::base().request_uri(REAL_BASE_DIR).'/'.$url;
 }
}
if(!function_exists("public_url")){
 function public_url($url=null){
   return site_url().(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']==80 ? GET_DIRS["DIRECTORY_ROOT"] : '').$url;
 }
}
if(!function_exists("route")){
 function route($name,$params=[]){
  return \OceanWT\Http\Route::url($name,$params);
 }
}
