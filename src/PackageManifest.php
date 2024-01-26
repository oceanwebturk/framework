<?php 

namespace OceanWT;

class PackageManifest{
 
 public function providers(): array
 {
  return $this->config("providers");
 }

 public function config(string $key)
 {
  return $this->getManifest()[$key];
 }

 public function getManifest(): array
 {
  $autoPackages=json_decode(file_get_contents(GET_DIRS['VENDOR']."composer/installed.json"),true)['packages'];
  $packages=[json_decode(file_get_contents(REAL_BASE_DIR."composer.json"),true)]+$autoPackages;
  for ($i=0;$i<count($packages);$i++){ 
   if(isset($packages[$i]['extra']['oceanwebturk'])){
   	return $packages[$i]['extra']['oceanwebturk'];
   }
  }
 }
}