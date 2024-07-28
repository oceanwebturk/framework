<?php 
namespace OceanWebTurk\Framework;

use Exception;
use function getcwd;
use function set_error_handler;
use function set_exception_handler;
use OceanWebTurk\Framework\Http\URL;
use OceanWebTurk\Framework\Config;
use OceanWebTurk\Framework\Console;
use OceanWebTurk\Framework\Support\Traits\Macro;
use OceanWebTurk\Framework\ApplicationServiceProvider;
use OceanWebTurk\Framework\Http\HttpServiceProvider;

/**
 * @codeCoverageIgnore
 */
class Application extends Container
{
  use Macro;
 
  /**
   * @var Console
   */
  public $cli;
  
  /**
   * @var string
   */
  public $rootDir;
  
  /**
   * @var string
   */
  public $projectType = 'SE';
  
  /**
   * @var array
   */
  public static $defines = [];
  
  /**
   * @var array
   */
  public static $namespaces = [];
  
  /**
   * @var array
   */
  public static $editors = ['subl' => ['url'=>'subl://{{$file}}:{{$line}}'],'vscode' => ['url' => 'vscode://file/{{$file}}:{{$line}}']];
  
  public function __construct(string $rootDir = null)
  {
    $this->rootDir = (is_null($rootDir) ? getcwd() : rtrim($rootDir,'/')).'/';
    self::setInstance($this);
    $this->cli = new Console();
  }
  
  /** 
   * @param array $defines
   * @return mixed
   */
  public function defines(array $defines = [])
  {
   self::$defines = $defines;
   return new self();
  }

  /**
   * @param array $namespaces
   * @return mixed
   */
  public function namespaces(array $namespaces = [])
  {
   self::$namespaces = $namespaces;
   return new self();
  }
  
  /**
   * @param mixed $provider
   * @param mixed $force
   * @return mixed
   */
  public function boot($provider,$force = false)
  {
    if(($registered = $this->getProvider($provider)) && ! $force){
      return $registered;
    }
   
    if (is_string($provider)) {
      $provider = $this->resolveProvider($provider);
    }
   
    $provider->boot();
    $this->services[get_class($provider)]=[
      'status' => true,
      'id' => md5(get_class($provider)),
      'added_method' => 'boot',
      'class' => $provider
    ];
    return $provider;
  }

  public function register($provider,$force = false)
  {
    if(($registered = $this->getProvider($provider)) && ! $force){
      return $registered;
    }
   
    if (is_string($provider)) {
     $provider = $this->resolveProvider($provider);
    }
  
    $provider->register();
    $this->services[get_class($provider)]=[
     'status' => true,
     'id' => md5(get_class($provider)),
     'added_method' => 'register',
     'class' => $provider
    ];
    return $provider;
  }
  
  /**
   * @param string $provider
   * @return object
   */
  public function resolveProvider(string $provider)
  {
   return new $provider($this);
  }
    
  /**
   * @param mixed $provider
   * @return array|mixed|null
   */
  public function getProvider($provider)
  {
   $name = is_string($provider) ? $provider : get_class($provider);
   return $this->services[$name] ?? null;
  }
  
  /**
   * @param string $type
   * @return mixed 
   */
  public function setType(string $type)
  {
   $this->projectType = $type;
   return new self();
  }

  /**
    * @param mixed $name
    * @return mixed
    */
  public function get($name)
  {
    if(isset($this->services[$name])){
      $service = $this->services[$name]['closure'];
      if(class_exists($service)){
        return new $service($this);
      }else{
        return $this->services[$name]['closure']();
      }
    }

    $reflector = new \ReflectionClass($name);
    if(!$reflector->isInstantiable()){
      throw new Exception("Class {$name} is not instantiable");
    }

    $constructor = $reflector->getConstructor();
    if(is_null($constructor)){
      return new $name;
    }
    
    $paramteres = $constructor->getParameters();
    $dependices = array_map(function($paramter){
      return $this->get($paramter->name);
    },$paramteres); 

    return $reflector->newInstanceArgs($dependices);
  }

  /**  
   * @return mixed
   */
  public function run()
  {
   ini_set('default_charset','UTF-8');
   define("PROJECT_TYPE",$this->projectType);

   if(function_exists("is_cli") && !is_cli()){
   session_start(['cookie_secure' => (URL::protocol() == 'https' ? true : false),'cookie_httponly' => true]);session_encode();}

   set_error_handler("OceanWebTurk\Framework\Application::errorHandler");
   set_exception_handler("OceanWebTurk\Framework\Application::exceptionHandler");
   define("REAL_BASE_DIR",$this->rootDir);

   $project_dirs = $this->projectType.'_Paths';
   define('GET_DIRS',self::$project_dirs());

   $project_namespace = $this->projectType.'_Namespaces';
   define('GET_NAMESPACES',self::$project_namespace());

   $this->coreAliases();
   define("COMPOSER_INSTALLED_FILE",GET_DIRS['VENDOR'].'composer/installed.json');

   $packages = file_exists(COMPOSER_INSTALLED_FILE) ? json_decode(file_get_contents(COMPOSER_INSTALLED_FILE))->packages : (Object) [];

   if(file_exists(COMPOSER_INSTALLED_FILE)){
    for($i=0;$i<count($packages);$i++){

     if($packages[$i]->name!="oceanwebturk/framework" && in_array($packages[$i]->type,['oceanwebturk-componet','oceanwebturk-package'])
      && isset($packages[$i]->extra->oceanwebturk) && $extra = $packages[$i]->extra->oceanwebturk){

      if(isset($extra->providers)){
        array_map(function($provider){
         $this->boot((new $provider($this)));
        },$extra->providers);
      }
      
      if(isset($extra->aliases)){
       foreach($extra->aliases as$alias=>$class){
        $this->alias($class,$alias);
       }
      }

     }
    } 
   }

   $appConfig = $this->get(Config::class)->get("app");
   array_map(function($provider){
    $class = GET_NAMESPACES['PROVIDERS'].str_replace([GET_DIRS['PROVIDERS'],'.php'],'',$provider);
    $this->boot((new $class($this)));
   },glob(GET_DIRS['PROVIDERS'].'*.php')); 

   if(is_cli()){
    echo $this->cli->init($this)->run(array_slice($_SERVER['argv'],1));
   }else{
    ob_start();
    echo (new Http\Route())->run();
    echo ob_get_clean();
   }
  }
  
  /**
   * @return array
   */
  public static function SE_Paths()
  {
   $path = [
    'APP' => REAL_BASE_DIR.'app/',
    'RESOURCES' => REAL_BASE_DIR.'resources/',
    'DATABASE' => REAL_BASE_DIR.'database/',
    'STORAGE' => REAL_BASE_DIR.'storage/',
   ];
   return self::$defines+$path+[
    'CONFIGS' => REAL_BASE_DIR.'etc/',
    'CONTROLLERS' => $path['APP'].'Controllers/',
    'PROVIDERS' => $path['APP'].'Providers/',
    'VIEWS' =>  $path['RESOURCES'].'views/',
    'LANGS' =>  $path['RESOURCES'].'langs/',
    'CACHE' =>  $path['STORAGE'].'cache/',
    'MIGRATION' =>  $path['DATABASE'].'migrations/',
    'SYSTEM' => __DIR__.'/',
    'VENDOR' => __DIR__.'/../../vendor/',
   ];
  }

  /**
   * @return array
   */
  public static function SE_Namespaces()
  {
   return self::$namespaces+[
    'CONTROLLERS' => 'App\\Controllers\\',
    'PROVIDERS' => 'App\\Providers\\',
   ];
  }
  
  /**
   * @return mixed
   */
  public static function errorHandler()
  {}
  
  /**
   * @return mixed
   */
  public static function exceptionHandler($e)
  {
   $message = '   Message & Code : '.$e->getCode().' | '.$e->getMessage().PHP_EOL;
   $message.= '   File & Line: '.$e->getFile().':'.$e->getLine().PHP_EOL;
   $message.= '   Traces: '.PHP_EOL;
   $trace = $e->getTrace();
   for ($i=0;$i<count($trace);$i++){ 
    $message.= '   File & Line: '.$trace[$i]['file'].' | '.$trace[$i]['line'].PHP_EOL;
   }
   if(is_cli()){
    echo PHP_EOL.$message;
   }else{
    $message = '   Message & Code : '.$e->getCode().' | '.$e->getMessage()."<br>";
    $message.= '   File & Line: <a href="'.str_replace(['{{$file}}','{{$line}}'],
    [$e->getFile(),$e->getLine()],self::$editors['subl']['url']).'">'.$e->getFile().':'.$e->getLine().'</a><br>';
    $message.= '   Traces: '."<br>";
    $trace = $e->getTrace();
    for ($i=0;$i<count($trace);$i++){ 
     $message.= '   File & Line: '.$trace[$i]['file'].' | '.$trace[$i]['line']."<br>";
    }
    echo $message;
   }
  }
  
  /**
   * @return mixed
   */
  private function coreAliases()
  {
   foreach([
    'URL' => URL::class
   ]as$key=>$alias){
   $this->alias($alias,$key);
  }

  array_map(function($provider){
    $this->boot((new $provider($this)));
  },[ApplicationServiceProvider::class,HttpServiceProvider::class,
  Database\DatabaseServiceProvider::class]); 
 }
}
