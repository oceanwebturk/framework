<?php
namespace OceanWebTurk\Framework\Support;

use OceanWebTurk\Framework\Support\Traits\Macro;

class Santos
{
 use Macro;
 
 private const SUFFIX = "santos";
 
 /**
  * @var array
  */
 public static $configs = [];
 
 /**
  * @var string
  */
 public $view;
 
 /**
  * @var string
  */
 public $viewName;
 
 /**
  * @var string
  */
 public $viewPath;
 
 /**
  * @var array
  */
 public $sections = [];

 /**
  * @var array
  */
 public $data = [];
 
 /**
  * @param array $configs
  * @return mixed
  */
 public function configs(array $configs = [])
 {
  self::$configs = $configs;
  return new self();
 }
 
 /**
  * @param string $name
  * @param array $params
  * @param array $options
  * @param boolean $extends
  * @return mixed
  */
 public function view(string $name,array $data = [],array $options = [],bool $extends = false)
 {
  extract($data); 
  self::$configs = array_merge(self::$configs,$options);

  if (!$extends) {
    $this->viewName = $name;
    $this->viewPath = self::$configs['view'].$this->parseViewName($name);
    $this->data = $data;
  }

  $viewPath = self::$configs['view'].$this->parseViewName($name);

  $this->view = file_get_contents($viewPath);
  $this->parse();

  $cachePath = self::$configs['cache'].md5($this->viewName).'.php';

  if(isset(self::$configs['cache_mode']) && self::$configs['cache_mode']==true){
  if (!file_exists($cachePath)) file_put_contents($cachePath, $this->view);
  if (
    filemtime($cachePath) < filemtime($viewPath) ||
    filemtime($cachePath) < filemtime($this->viewPath)
  ) {
    file_put_contents($cachePath, $this->view);
  }
  }else{
    echo eval('?>'.$this->view);
  }

  if (!$extends){
    ob_start();
    require $cachePath;
    return ob_get_clean();
  }
 }
 
 /**
  * @param string $name
  * @return string
  */
 private function parseViewName(string $name)
 {
  return $name.'.'.ltrim((isset(self::$configs['suffix']) ? self::$configs['suffix'] : self::SUFFIX),'.');
 }
 
 /**
  * @return mixed
  */
 private function parse()
 {
  $this->view = preg_replace_callback('/@include\(\'(.*?)\'\)/',function($viewName){
  return file_get_contents($this->configs['viewPath'] . '/' . $this->parseViewName($viewName[1]));},$this->view);
  $this->view = str_replace(["{{","}}"],["<?php echo ","; ?>"],$this->view);  

  $this->view = preg_replace("/@if\s*\((.*?)\)\s*/","<?php if($1): ?>",$this->view);
  $this->view = preg_replace("/@isset\s*\((.*?)\)\s*/","<?php if(isset($1)): ?>",$this->view);
  $this->view = str_replace("@else","<?php else: ?>",$this->view);
  $this->view = str_replace("@endif","<?php endif; ?>",$this->view);
  
  $this->view = preg_replace("/@foreach\s*\((.*?)\)\s*/","<?php foreach($1): ?>",$this->view);  
  $this->view = str_replace("@endforeach","<?php endforeach; ?>",$this->view);

  $this->view = preg_replace("/@dd\s*\((.*?)\)\s*/","<?php dd($1); ?>",$this->view);

  $this->view = preg_replace("/@json\s*\((.*?)\)\s*/","<?php json_encode($1); ?>",$this->view);

  $this->view = preg_replace("/@lang\s*\((.*?)\)\s*/","<?php echo lang($1); ?>",$this->view);

  $this->view = preg_replace_callback('/@section\(\'(.*?)\', \'(.*?)\'\)/', function ($sectionDetail) {
    $this->sections[$sectionDetail[1]] = $sectionDetail[2];
    return '';
  },$this->view);

  $this->view = preg_replace_callback('/@section\(\'(.*?)\'\)(.*?)@endsection/s', function ($sectionName) {
    $this->sections[$sectionName[1]] = $sectionName[2];
    return '';
  },$this->view);

  $this->view = preg_replace_callback('/@extends\(\'(.*?)\'\)/', function ($viewName) {
    $this->view($viewName[1], $this->data,[],true);
    return '';
  }, $this->view);

  $this->view = preg_replace_callback('/@yield\(\'(.*?)\'\)/', function ($yieldName) {
    return $this->sections[$yieldName[1]] ?? '';
  }, $this->view);
 }
}
