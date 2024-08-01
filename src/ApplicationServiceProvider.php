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

  $this->app->get(Santos::class)->configs([
    'view' => GET_DIRS['VIEWS'],
    'cache' => GET_DIRS['CACHE'],
    'cache_mode' => true
  ]);
   
  $this->app->get(Santos::class)->addPath(GET_DIRS['SYSTEM'].'../views/','system');
  $this->app->get(Lang::class)->addPath(GET_DIRS['LANGS'],'default',[
    'lang' => config("app")['lang']
  ]);

  $this->app->get(Lang::class)->addPath(__DIR__.'/../langs/','system',[
    'lang' => config("system:system")['lang']
  ]);
 }
}