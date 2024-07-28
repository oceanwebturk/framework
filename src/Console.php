<?php
namespace OceanWebTurk\Framework;

class Console
{
  /**
   * @var Application
   */
  public $app;

  /**
   * @var array
   */
  public $commands = [];
  
  /**
   * @param mixed $app
   * @return mixed
   */
  public function init($app)
  {
    $this->app = $app;
    return $this;
  }
   
  /**
   * @param string $name
   * @param mixed $callback
   * @param array $options
   * @return void
   */
  public function command(string $name,$callback,array $options = [])
  {
   $this->commands[$name] = [
    'callback' => $callback,
    'options' => $options
   ];
  }

  public function run(array $args = [])
  {
   $prefix = __NAMESPACE__.'\\Commands\\';
   foreach(glob(__DIR__."/Commands/*.php")as$class){
    $class = str_replace([__DIR__.'/Commands/','.php','Command'],'',$class);
    $cmdClass = $prefix.$class;
    
    if(class_exists($cmdClass)){
      $this->commands[strtolower($class)] = [
        'callback' => [$prefix.$class,'run'],
        'options' => []
      ];
    }elseif(class_exists($cmdClass.'Command')){
      $this->commands[strtolower($class)] = [
        'callback' => [$prefix.$class.'Command','run'],
        'options' => []
      ];
    }
    unset($this->commands['base']);
   }

   $GLOBALS['_OCEANWEBTURK']['COMMANDS'] = $this->commands;
   $this->runCommand($args);
  }
  
  /**
   * @param array $args
   * @return mixed
   */
  private function runCommand(array $args = [])
  {
   $name = isset($args[0]) ? $args[0] : 'help';
   if(isset($this->commands[$name])){
    $method = $this->commands[$name]['callback'];
    if(is_array($method)){
      $class = $method[0];
      $class = new $class($this->app,$this);
      echo call_user_func_array([$class,(isset($method[1]) ? $method[1] : 'run')],[$args]);
    }
   }
  }
}