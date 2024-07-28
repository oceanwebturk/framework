<?php 
namespace OceanWebTurk\Framework\Http;

use OceanWebTurk\Framework\Config;
use OceanWebTurk\Framework\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    
 /**
  * @return mixed
  */
 public function boot()
 {
  new Route();
  $this->app->alias(Controller::class,'Controller');
  $this->app->get(Config::class)->get("routes");
 }
}