<?php

namespace OceanWT;

class TemplateEngine
{ 
    use Support\Traits\Macro;

    /**
     * @var array
     */
    protected $configs=[];

    public static function render($name, $data = [])
    {
     extract($data);
     if(is_array($name)) {
      $file=$name['path'].$name['file'];
     }else{
      $file=GET_DIRS["VIEWS"].$name;
     }
     include($file.".php");
    }
}
