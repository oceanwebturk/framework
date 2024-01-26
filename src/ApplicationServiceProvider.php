<?php

namespace OceanWT;

class ApplicationServiceProvider extends Support\ServiceProvider
{
 public function boot()
 {
  $GLOBALS['_OCEANWEBTURK'] = [];
  new Import();
 }
}
