<?php

namespace OceanWT;

class ApplicationServiceProvider extends Support\ServiceProvider
{
 public function boot()
 {
  new Import();
 }
}
