<?php 

return [
 //composer require league/plates
 'plates' => function(string $name,array $data=[]){
  $template=new \League\Plates\Engine(GET_DIRS['VIEWS']);
  echo $template->render($name,$data);
 }
];