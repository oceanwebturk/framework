<?php

namespace OceanWebTurk;

class ApplicationServiceProvider extends Support\ServiceProvider
{
 public function boot()
 {
  new Import();
 }
}
