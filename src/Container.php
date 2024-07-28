<?php 
namespace OceanWebTurk\Framework;

use Exception;

class Container
{   
    /**
     * @var array
     */
    protected $bindings = []; 
    
    /**
     * @var object|null
     */
    public static $instance = null;
    
    /**
     * @var array
     */
    protected $services = [];
    
    /**
     * @return mixed
     */ 
    protected $aliases = [];
    
    /**
     * @param mixed $abstract
     * @param string $key
     * @return mixed
     */
    public function alias($abstract,string $key)
    {
     $this->aliases[$key] = $abstract;
     if(class_exists($abstract)){
       class_alias($abstract,$key);
     }
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
     if (is_null(static::$instance)) {
       static::$instance = new static;
     }
     return static::$instance;
    }

    /**
     * @param object|null $container
     * @return mixed
     */
    public static function setInstance($container = null)
    {
     return static::$instance = $container;
    }

    /**
     * @return mixed
     */
    public function bind($key, $resolver)
    {
     $this->bindings[$key] = $resolver;
     return $this;
    }

    /**
     * @return mixed
     */
    public function resolve($key)
    {
     if(array_key_exists($key,$this->bindings))
     {
      $resolver = $this->bindings[$key];
      return call_user_func($resolver);
     }
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
}