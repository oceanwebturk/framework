<?php

namespace OceanWT;

class Config
{
     use Support\Traits\Macro;

    /**
     * @var array
     */
    public static $paths = [
     GET_DIRS['CONFIGS'],
     'root'=>REAL_BASE_DIR,
    ];

    /**
     * @var array
     */
    public static $namespaces = [
     GET_NAMESPACES['CONFIGS']
    ];

    /**
     * @var string|object|boolean
     */
    protected static $default=false;

    /**
     * @var string
     */
    protected static $type="Config";

    /**
     * @param string $path
     */
    public static function addPath($path,$prefix=null)
    {
        self::$paths[$prefix] = $path;
        return new self();
    }

    /**
     * @param string $namespace
     */
    public static function addNamespace($namespace)
    {
        self::$namespaces[] = $namespace;
        return new self();
    }

    /**
     * @param $class
     */
    public static function default($class)
    {
      self::$default=$class;
      return new self;
    }
    
    /**
     * @param  string $name
     */
    public static function get($name)
    {
      $explode=self::explodePath($name);
      $namespaces=self::$namespaces;
      for ($i=0;$i<count($namespaces);$i++) {
        $class=$namespaces[$i].ucfirst($name);
        if(class_exists($class)){
         $return=new $class();
        }elseif(class_exists($name)){
         $return=new $name();
        }
      }
      $file=$explode[0].(isset($explode[1]) ? $explode[1] : $name).(isset(self::initDriver()->fileExtension) ? self::initDriver()->fileExtension : ".php");
      $return=self::initDriver()->getFile($file);
      if($default=self::getDefault()){
       $return=$return + $default;
      }
      return (Object) $return;
    }

    /**
     * @return object|boolean
     */
    private static function getDefault()
    {
     $default=self::$default;
     self::$default=NULL;
     if(is_string($default)){
      return get_class_vars($default);
     }elseif(is_object($default)){
      return get_object_vars($default); // @codeCoverageIgnore
     }else{
      return false;
     }
    }

    /**
     * @param string $driver
     */
    public static function setDriver(string $driver)
    {
     self::$type=ucfirst($driver);
     return new self;
    }

    /**
     * @return object
     */
    private static function initDriver()
    {
     $class=__NAMESPACE__.'\\Config\\'.self::$type;
     if(class_exists($class)){
      return new $class();
     }
    }

    /**
     * @param  string $name
     */
    private static function explodePath(string $name)
    {
     if(strpos($name,"::")){
      $ex=explode("::",$name);
      $path=self::$paths[$ex[0]];
      $return=[$path,$ex[1]];
     }else{
      $return=[self::$paths[0]];
     }
     return $return;
    }
}
