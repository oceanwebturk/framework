<?php

namespace OceanWebTurk;

class ApplicationServiceProvider extends Support\ServiceProvider
{
 public function boot()
 {
  session_start();
  new Import();
 }
}
