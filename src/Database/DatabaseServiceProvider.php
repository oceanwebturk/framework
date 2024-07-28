<?php
namespace OceanWebTurk\Framework\Database;

use OceanWebTurk\Framework\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
  /** 
   * @return mixed
   */
  public function boot()
  {   
   $db = new DB();
   $dbConfig = config("database");
   foreach($dbConfig['connections']as$key=>$value){
    $db->addConnection($value,($dbConfig['default'] == $key ? 'default' : $key));
   }
   $db->init();
   $this->app->alias(DB::class,'DB');
   $this->app->alias(Model::class,'Model');
   $this->app->cli->command('migrate',[DatabaseCommands::class,'migrate']);
  }
}