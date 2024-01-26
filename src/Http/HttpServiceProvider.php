<?php

namespace OceanWT\Http;

use OceanWT\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{

 /**
  * @return void
  */
 public function boot(): void
 {
  $this->cli->command("make:controller",[HttpCommand::class,'controller'],[
   "description"=>"Make a Controller File"
  ]);
 }
}
