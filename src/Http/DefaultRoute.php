<?php

namespace OceanWebTurk\Http;

class DefaultRoute{
 public $autoRoute=false;
 public $defaultFunction="index";
 public $defaultNamespace=GET_NAMESPACES['CONTROLLERS'];
 public $uriReplaceCharacters=[
  '../' => '',
  './'  => ''
 ];
}
