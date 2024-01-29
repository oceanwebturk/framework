<?php

namespace OceanWT\Support;

use OceanWT\Import;
use OceanWT\Lang;

class ServiceProvider
{
    use Traits\Macro;
    /**
     * @var \OceanWT\OceanWT
     */
    public $app;
    
    /**
     * @var \OceanWT\Http\Route
     */
    public $route;

    /**
     * @var \OceanWT\Console
     */
    public $cli;

    /**
     * @var array
     */
    public static $providers = [];
    
    public function __construct()
    {
     $this->app=new \OceanWT\OceanWT();
     $this->cli=new \OceanWT\Console();
     $this->route=new \OceanWT\Http\Route();
    }

    public static function default()
    { 
        self::$providers = [
         \OceanWT\ApplicationServiceProvider::class,
         \OceanWT\Http\HttpServiceProvider::class,
         \OceanWT\Database\DatabaseServiceProvider::class,
        ];
        return new self();
    }

    /**
     * @param  array  $providers
     */
    public static function merge(array $providers)
    {
        self::$providers = array_merge(self::$providers, $providers);
        return new self();
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        return self::$providers;
    }
    
    /**
     * @param  string $name
     */
    public static function loadRoutes(string $name)
    {
        require($name);
    }

    /**
     * @param string $path
     * @param string $namespace
     * @param string $templateEngine
     */
    public static function loadViews(string $path,string $namespace,string $templateEngine='system')
    {
     foreach(Import::$paths as$folder){
      $folder=is_array($folder) ? $folder['path'] : $folder;
      $dir=$folder.'vendor/'.$namespace.'/';
      if(is_dir($dir)){
        $path=$dir;
      }
     }
     Import::$paths[$namespace]=['path'=>$path,'templateEngine'=>$templateEngine];
    }

    /**
     * @param  string $path
     * @param  string $namespace
     */
    public static function loadLangs(string $path,string $namespace)
    {
     Lang::$paths[$namespace]=$path;
    }
}
