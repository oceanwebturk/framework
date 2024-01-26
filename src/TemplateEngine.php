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
     include(GET_DIRS["VIEWS"].$name.".php");
    }
}
