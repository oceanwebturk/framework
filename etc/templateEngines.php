<?php 

return [
 'system'=>[\OceanWT\Import::class,'render'],
 // composer require twig/twig
 'twig'=>function(string $name,array $data=[]){
  if(is_array($name)){
   $name=$name['file'];
   $viewPath=$name['path'];
  }else{
  $viewPath=GET_DIRS['VIEWS'];
  }
  $loader=new \Twig\Loader\FilesystemLoader($viewPath);
  $twig=new \Twig\Environment($loader,[
    'cache'=> isset($cachePath) ? $cachePath : GET_DIRS['CACHES'],
  ]);
  echo $twig->render($name,$data);
 },
 // composer require jenssegers/blade
 'blade'=>function(string $name,array $data=[]){
  if(is_array($name)){
   $name=$name['file'];
   $viewPath=$name['path'];
  }else{
   $viewPath=GET_DIRS['VIEWS'];
  }
  $blade=new Jenssegers\Blade\Blade($viewPath,(isset($cachePath) ? $cachePath : GET_DIRS['CACHES']));
  echo $blade->render($name,$data);
 },
 //composer require league/plates
 'plates' => function(string $name,array $data=[]){
  if(is_array($name)){
   $file=$name['file'];
   $path=$name['path'];
  }else{
   $path=GET_DIRS['VIEWS'];
  }
  $template=new \League\Plates\Engine($path);
  echo $template->render($file,$data);
 }
];