<?php
namespace OceanWT;

use OceanWT\Support\Lang;

class OceanWT extends Container
{
    /**
     * @var \OceanWT\Autoloader
     */
    public $autoloader;

    /**
     * @var \OceanWT\OceanWT
     */
    public static $app;

    /**
     * @var string
     */
    public static $basePath;

    /**
     * @var array
     */
    public static $configs = [],$defines = [],$namespaces = [];
    
    /**
     * @var array
     */
    public static $serviceProviders = [];

    /**
     * @param string|null $rootDir
     */
    public function __construct(string $rootDir = null)
    {
     self::$app = $this;
     $this->autoloader = new Autoloader();
     if(isset($rootDir)){
      $this->setBasePath($rootDir);
     }
     $this->registerBaseBindings();
    }

    /**
     * @param  array $configs
     * @return \OceanWT\OceanWT
     */
    public static function configs(array $configs)
    {
        self::$configs = $configs;
        return new self();
    }

    /**
     * @param  array  $defines
     * @return \OceanWT\OceanWT
     */
    public static function defines(array $defines)
    {
        self::$defines = $defines;
        return new self();
    }

    /**
     * @param  array  $namespaces
     * @return \OceanWT\OceanWT
     */
    public static function namespaces(array $namespaces)
    {
        self::$namespaces = $namespaces;
        return new self();
    }

    /**
     * @param string $app
     */
    public static function setApplication(string $app)
    {
        self::$application = $app;
        return new self();
    }

    /**
     * @return void
     */
    protected function registerBaseBindings(): void
    {
     ini_set("default_charset","UTF-8");
     static::setInstance($this);
     $this->instance('app',$this);
     $this->instance(Container::class,$this);
    }

    /**
     * @param string $path
     */
    public function setBasePath(string $basePath)
    {
     self::$basePath=$basePath;
    }

    /**
     * @return void
     */
    public static function init()
    {
     !defined("REAL_BASE_DIR") ? define('REAL_BASE_DIR', self::$basePath) : '';
     !defined("GET_DIRS") ? define('GET_DIRS', self::getPaths()) : '';
     !defined("GET_NAMESPACES") ? define('GET_NAMESPACES', self::getNamespaces()) : '';
     !defined("GET_CONFIGS") ? define('GET_CONFIGS', self::getConfigs()) : '';
     foreach(GET_NAMESPACES as$nKey => $nVal) {
      self::$app->autoloader->addNamespace($nKey, $nVal);
      if(isset(GET_DIRS[$nKey])) {
       self::$app->autoloader->addNamespace(GET_NAMESPACES[$nKey], GET_DIRS[$nKey]);
      }
     }
     self::$app->autoloader->register();
     do_action("system_init");
     self::$serviceProviders=array_merge(Config::get("app")->providers,(new PackageManifest)->providers());
    }

    /**
     * @param  string|null $routeMode
     */
    public function run(string $routeMode=null)
    {
     self::init();

     if(!Config::get("view")->default || Config::get("view")->default=="view"){
      self::templateEngine([(new TemplateEngine()),'render']);
     }elseif(Config::get("view")->engines[Config::get("view")->default]){
      self::templateEngine(Config::get("view")->engines[Config::get("view")->default]);
     }

     if(isset(GET_CONFIGS['composer_autoload']) && GET_CONFIGS['composer_autoload']==true && file_exists(GET_DIRS['VENDOR'].'autoload.php')){
      include(GET_DIRS['VENDOR'].'autoload.php');
     }

     if(Config::get("app")->mode=="development"){
      if(is_cli()){
       set_error_handler("\OceanWT\Console::errorHandler");
       set_exception_handler("\OceanWT\Console::exceptionHandler");
      }else{
       $this->webHandler();
      }
     }
     if(version_compare(PHP_VERSION,REQUIRED_PHP_VERSION,'<')){
      throw new \Exception(sprintf(lang("system::php_version_control"),REQUIRED_PHP_VERSION), 1);
     }
     self::setLocale(Config::get("app")->lang);
     self::providerLists();
     if(is_cli()){
      Console::run($_SERVER['argv']);
     }else{
      $this->web($routeMode);
     }
    }
    
    /**
     * @param string $methodName
     */
    protected static function providerLists(string $methodName = "boot")
    {
     for($i = 0;$i < count(self::$serviceProviders);$i++) {
      $class = self::$serviceProviders[$i];
      $class = new $class();
      if(method_exists($class, $methodName)) {
       echo $class->$methodName();
      }
     }
    }

    /**
     * @return array
     */
    public static function getPaths()
    {
     $paths=['APP'=>REAL_BASE_DIR.'app/','VAR'=>REAL_BASE_DIR.'var/','DATABASE'=>REAL_BASE_DIR.'database/'];
     return self::$defines+$paths+[
       "DIRECTORY_ROOT" => "public/",
       "CONFIGS" => REAL_BASE_DIR."etc/",
       "VENDOR" => REAL_BASE_DIR."vendor/",
       "SYSTEM" => __DIR__."/",
       "LOGS" => $paths["VAR"]."logs/",
       "VIEWS" => $paths["VAR"]."html/",
       "LANGS" => $paths["VAR"]."langs/",
       "CACHES" => $paths["VAR"]."cache/",
       "MODELS" => $paths["APP"]."Models/",
       "BOOTSTRAP"=> is_dir(REAL_BASE_DIR.'.oceanwebturk') ? REAL_BASE_DIR.'.oceanwebturk' : REAL_BASE_DIR.'bootstrap/',
       "SERVICES" => $paths["APP"]."Providers/",
       "CONTROLLERS" => $paths["APP"]."Controllers/",
       "MIGRATIONS" => $paths['DATABASE']."migrations/",
     ]+(self::getApplication() && is_callable([self::getApplication(),"paths"]) ? call_user_func([self::getApplication(),"paths"]) : []);
    }

    /**
     * @return array
     */
    public static function getNamespaces()
    {
     $namespaces=['APP'=>"App\\"];
     return self::$namespaces+$namespaces+[
      "CONFIGS" => "Config\\",
      "SERVICES" => $namespaces['APP']."Providers\\",
      "CONTROLLERS" => $namespaces['APP']."Controllers\\",
      "MODELS" => $namespaces['APP']."Models\\",
     ]+(self::getApplication() && is_callable([self::getApplication(),"namespaces"]) ? call_user_func([self::getApplication(),"namespaces"]) : []);
    }
    
    /**
     * @return array
     */
    public static function getConfigs()
    {
     return self::$configs+(Array) Config::get("app");
    }

    /**
     * @param  object $e
     * @return string
     */
    public static function webExceptionHandler(object $e)
    {
      ob_start();
      $file=$e->getFile();
      $line=$e->getLine();
      $message=$e->getMessage();
      include(__DIR__.'/Views/layout-handler.php');
      echo minify(ob_get_clean()); 
      exit;
    }
    
    /**
     * @param  int    $no     
     * @param  string $message
     * @param  string $file   
     * @param  int    $line   
     * @return string
     */
    public static function webErrorHandler(int $no,string $message,string $file,int $line)
    {
      ob_start();
      include(__DIR__.'/Views/layout-handler.php');
      echo minify(ob_get_clean());
      exit;
    }
    
    /**
     * @param string|callable|array $engine
     */
    public static function templateEngine(string|callable|array $engine)
    {
     self::$configs['templateEngine']=$engine;
     return new self;
    }

    /**
     * @param  string $locale
     */
    public static function setLocale(string $locale)
    {
      Lang::$appLang=$locale;
    }

    /**
     * @param array $params
     * @return void
     */
    public function upgrade(array $params=[])
    {
     $hooks=[];
     if(!isset(Config::setDriver("json")->get("repos")->scalar)){
      $hooks=array_merge($hooks,(Array) Config::setDriver("json")->get("repos"));
     }
     if(isset(Config::setDriver("json")->get("root::oceanwebturk")->repos)){
      $hooks=array_merge($hooks,(Array) Config::setDriver("json")->get("root::oceanwebturk")->repos);
     }
     $urls=[
     'skeleton'=>['type'=>'git','url'=>'https://github.com/oceanwebturk/oceanwebturk','path'=>REAL_BASE_DIR],
     'system'=>['type'=>'git','url'=>'https://github.com/oceanwebturk/framework','path'=>GET_DIRS['SYSTEM']]
     ]+(Array) $hooks;
    }

    /**
     * @param string|object $abstract
     * @param bool|boolean $force
     */
    public function register(string|object $abstract,bool $force=false)
    {
     if(is_string($abstract)){
      $provider=$this->resolvedProvider($abstract);
     }
     $provider->register();
    }

    /**
     * @param  string $provider
     */
    public function resolvedProvider(string $provider)
    {
     return new $provider($this);
    }

    /**
     * @param string|null $routeMode
     * @return void
     */
    private function web(string $routeMode=null)
    {
     ob_start();
     echo self::getApplication() ? call_user_func([self::getApplication(),'execute']) : '';
     Http\Route::mode($routeMode)->run();
     echo \minify(ob_get_clean(),(isset(self::getApplication()->minify) ? self::getApplication()->minify : Config::setDriver("config")->default(__NAMESPACE__."\\DefaultApp")->get("app")->minify));
    }

    /**
     * @return void
     */
    private function webHandler(): void
    {
     set_error_handler("\OceanWT\OceanWT::webErrorHandler");
     set_exception_handler("\OceanWT\OceanWT::webExceptionHandler");
    }
}
