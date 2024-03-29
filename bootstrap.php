<?php
defined('OCEANWEBTURK_VERSION') || define("OCEANWEBTURK_VERSION", "0.2.3");
defined('REQUIRED_PHP_VERSION') || define("REQUIRED_PHP_VERSION","7.4");
$GLOBALS['_OCEANWEBTURK'] = [
 'BLACKBOX_AI_URL' => 'https://www.blackbox.ai/agent/OceanWebTurkVzldXUh'
];
if(!function_exists("str")){
 function str($str='')
 {
  return OceanWebTurk\Support\Str::text($str);
 }
}

if(!function_exists("request_uri")){
function request_uri($path = __DIR__)
{
    $root = "";
    $dir = strtr('\\', '/', realpath($path));
    if(!empty($_SERVER['CONTEXT_PREFIX'])) {
        $root .= $_SERVER['CONTEXT_PREFIX'];
        $root .= substr($dir, strlen($_SERVER['CONTEXT_DOCUMENT_ROOT']));
    } else {
        $root .= substr($dir, strlen($_SERVER['DOCUMENT_ROOT']));
    }
    return $root;
}
}

if(!function_exists("minify")){
function minify($data, $st = true)
{
    if($st) {
        return preg_replace(array(
        '/ {2,}/',
        '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
        ), array(' ',''), $data);
    } else {
        return $data;
    }
}
}

if(!function_exists("is_cli")){
 function is_cli(){
  if(in_array(PHP_SAPI,['php-cli','cli'])){
   return true;
  }else{
   return false;
  }
 }
}

if(!function_exists("dd")){
 function dd($text){
  echo "<pre>";
  print_r($text);
  echo "</pre>";
 }
}

use OceanWebTurk\Hook;
use OceanWebTurk\Http\Route;
use OceanWebTurk\Support\Lang;

if(!function_exists("app")){
 function app($abstract=null,array $params=[]){
  return \OceanWebTurk\OceanWebTurk::getInstance()->make($abstract,$params);
 }
}

if(!function_exists("view")){
 function view(string $name,array $data = array()){
   \OceanWebTurk\Import::view($name, $data);
 }
}

if(!function_exists("route")){
 function route(string $name,array $data = array()){
   return Route::url($name, $data);
 }
}

if(!function_exists("config")){
 function config(string $config){
  return \OceanWebTurk\Config::get($config);
 }
}

if(!function_exists("do_action")){
 function do_action(string $name,array $args = array()){
  Hook::trigger($name, $args);
 }
}

if(!function_exists("add_action")){
 function add_action(string $name,$callback){
    Hook::add($name, $callback);
 }
}

if(!function_exists("remove_action")){
 function remove_action(string $name,$callback){
     Hook::remove($name, $callback);
 }
}

if(!function_exists("lang")){
 function lang(string $name){
  return Lang::get($name);
 }
}

OceanWebTurk\Console::$commands=[
 'help'=>['action'=>OceanWebTurk\Commands\HelpCommand::class,'description'=>'Help'],
 'make:provider'=>['action'=>[OceanWebTurk\Commands\MakeCommand::class,'provider'],'description'=>'Make a Provider File'],
 'serve'=>['action'=>[OceanWebTurk\Commands\HelpCommand::class,'serve'],'description'=>'Application development server'],
 'upgrade'=>['action'=>[OceanWebTurk\OceanWebTurk::class,'upgrade'],'description'=>'System Upgrade'],
];
