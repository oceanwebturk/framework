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
     * @var string
     */
    public static $projectType="FS";

    /**
     * @var array
     */
    public static $configs = [],$defines = [],$namespaces = [];
    
    /**
     * @var array
     */
    public static $serviceProviders = [],$supportedProjectTypes = ["FS","EP"];
    
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
     * @param string $projectType
     */
    public static function projectType(string $projectType)
    {
     self::$projectType=$projectType;
     return new self;
    }

    /**
     * @return void
     */
    protected function registerBaseBindings(): void
    {
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
     ini_set("default_charset","UTF-8");
     define('REAL_BASE_DIR', self::$basePath);
     self::selectedProjectActions();
     self::runAutoloader();
     self::$serviceProviders=array_merge(Config::get("app")->providers,(new PackageManifest)->providers());     
     do_action("system_init");
    }

    /**
     * @param  string|null $routeMode
     */
    public function run(string $routeMode=null)
    {
     self::init();
     self::templateEngine(Config::get("app")->defaultTemplateEngine);
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
    public static function providerLists(string $methodName = "boot")
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
       "LOGS" => $paths["VAR"]."logs/",
       "VIEWS" => $paths["VAR"]."html/",
       "LANGS" => $paths["VAR"]."langs/",
       "CACHES" => $paths["VAR"]."cache/",
       "MODELS" => $paths["APP"]."Models/",
       "BOOTSTRAP"=> is_dir(REAL_BASE_DIR.'.oceanwebturk') ? REAL_BASE_DIR.'.oceanwebturk' : REAL_BASE_DIR.'bootstrap/',
       "SERVICES" => $paths["APP"]."Providers/",
       "CONTROLLERS" => $paths["APP"]."Controllers/",
       "MIGRATIONS" => $paths['DATABASE']."migrations/",
       "SYSTEM" => __DIR__."/",
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
     'system'=>['type'=>'git','url'=>'https://github.com/oceanwebturk/framework','path'=>self::getPaths()['SYSTEM']]
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
    public function web(string $routeMode=null)
    {
     ob_start();
     echo self::getApplication() ? call_user_func([self::getApplication(),'execute']) : '';
     Http\Route::mode($routeMode)->run();
     echo \minify(ob_get_clean(),(isset(self::getApplication()->minify) ? self::getApplication()->minify : (isset($GLOBALS['_OCEANWEBTURK']['MINIFY']) ? $GLOBALS['_OCEANWEBTURK']['MINIFY'] : Config::setDriver("config")->get("app")->minify)));
    }

    /**
     * @return void
     */
    public function webHandler(): void
    {
     set_error_handler("\OceanWT\OceanWT::webErrorHandler");
     set_exception_handler("\OceanWT\OceanWT::webExceptionHandler");
    }
    
    /**
     * @return void
     */
    public static function runAutoloader(): void 
    {
     define('GET_DIRS', self::getPaths());
     define('GET_NAMESPACES', self::getNamespaces());
     define('GET_CONFIGS', self::getConfigs());
     foreach(self::getNamespaces()as$nKey => $nVal) {
      self::$app->autoloader->addNamespace($nKey, $nVal);
      if(isset(self::getPaths()[$nKey])) {
       self::$app->autoloader->addNamespace(self::getNamespaces()[$nKey],self::getPaths()[$nKey]);
      }
     }
     self::$app->autoloader->register();
     if(isset(self::getConfigs()['composer_autoload']) && self::getConfigs()['composer_autoload']==true && file_exists(self::getPaths()['VENDOR'].'autoload.php')){
      include(self::getPaths()['VENDOR'].'autoload.php');
     }
    }
    
    public static function selectedProjectActions()
    {
     $projectCallbacks=[
      'global' => self::$projectType.'_PROJECT',
      'dirs' => self::$projectType.'_PROJECT_DIRS', 
      'namespaces' => self::$projectType.'_PROJECT_NAMESPACES', 
     ];
     if(!in_array(self::$projectType,self::$supportedProjectTypes)){
      http_response_code(500);
      exit();
     }
     return array_map(function($callback){
      echo call_user_func([new self(),$callback]); 
     },$projectCallbacks);
    }
    
    /**
     * @return void
     */
    public function EP_PROJECT(): void
    {
     self::$defines=array_merge(self::$defines,['PROJECTS'=>REAL_BASE_DIR.'projects/']);
     self::$namespaces=array_merge(self::$namespaces,['PROJECTS'=>'Projects\\']);
    }
}
