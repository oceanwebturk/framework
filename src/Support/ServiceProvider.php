<?php

namespace OceanWebTurk\Support;

use OceanWebTurk\Import;
use OceanWebTurk\Lang;

class ServiceProvider
{
    use Traits\Macro;
    /**
     * @var \OceanWebTurk\OceanWebTurk
     */
    public $app;
    
    /**
     * @var \OceanWebTurk\Http\Route
     */
    public $route;

    /**
     * @var \OceanWebTurk\Console
     */
    public $cli;

    /**
     * @var array
     */
    public static $providers = [];
    
    public function __construct()
    {
     $this->app=new \OceanWebTurk\OceanWebTurk();
     $this->cli=new \OceanWebTurk\Console();
     $this->route=new \OceanWebTurk\Http\Route();
    }

    public static function default()
    { 
        self::$providers = [
         \OceanWebTurk\ApplicationServiceProvider::class,
         \OceanWebTurk\Http\HttpServiceProvider::class,
         \OceanWebTurk\Database\DatabaseServiceProvider::class,
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
