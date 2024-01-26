<?php
namespace OceanWT;

class DefaultEngines
{
 /**
  * Package: composer require league/plates
  * @param  string $name
  * @param  array  $data
  * @return string|null
  */
 public function plates(string $name,array $data=[]): string|null
 {
    $template=new \League\Plates\Engine(GET_DIRS['VIEWS']);
    echo $template->render($name,$data);
 }
}
