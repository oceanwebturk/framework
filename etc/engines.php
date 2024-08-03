<?php 

use OceanWebTurk\Framework\Support\Santos;

return [
  'template' => [
    'santos' => [
      'callback' => function(array $argv = [],array $data = [],array $options = []){
       return (new Santos())->configs($argv['options'])->view($argv['name'],$data,$options);
      }
    ]
  ]
];