<?php 

return [
 'system'=>[
  'package' => 'oceanwebturk/framework',
  'exec' => 'composer require {{package}}',
  'action' => [\OceanWebTurk\Import::class,'render']
 ],
 'twig'=>[
  'package' => 'twig/twig',
  'exec' => 'composer require {{package}}',
  'action' => function(array $args,array $data=[]){
   $loader=new \Twig\Loader\FilesystemLoader($args['viewPath']);
   $twig=new \Twig\Environment($loader,[
     'cache'=> $args['cachePath'],
   ]);
   echo $twig->render($args['name'],$data);
  }
 ],
 'blade'=>[
  'package' => 'jenssegers/blade',
  'exec' => 'composer require {{package}}',
  'action' => function(array $args,array $data=[]){
    $blade=new Jenssegers\Blade\Blade($args['viewPath'],$args['cachePath']);
    echo $blade->render($name,$data);
  }
 ],
 'plates' => [
  'package' => 'league/plates',
  'exec' => 'composer require {{package}}',
  'action' => function(array $name,array $data=[]){
   $template=new \League\Plates\Engine($args['viewPath']);
   echo $template->render($args['name'],$data);
  }
 ],
];
