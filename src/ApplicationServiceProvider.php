<?php 
namespace OceanWebTurk\Framework;

use OceanWebTurk\Framework\Support\Lang;
use OceanWebTurk\Framework\Support\Santos;
use OceanWebTurk\Framework\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    
 /**
  * @return mixed
  */
 public function boot()
 {
  $this->app->get(Config::class)->addPath(GET_DIRS['CONFIGS']);
  $this->app->get(Config::class)->addPath(GET_DIRS['SYSTEM'].'../etc/','system');
  
  Import::setEngines(config('system:engines')['template']);
  Import::addPath(GET_DIRS['VIEWS'],'default',['cache' => GET_DIRS['CACHE'],'cache_mode' => true]);
  Import::addPath(GET_DIRS['SYSTEM'].'../views/','system');
  
  Lang::addPath(GET_DIRS['LANGS'],'default',[
    'lang' => config("app")['lang']
  ]);

  Lang::addPath(__DIR__.'/../langs/','system',[
    'lang' => config("system:system")['lang']
  ]);
 }
}