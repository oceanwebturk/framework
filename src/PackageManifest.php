<?php 

namespace OceanWebTurk;

class PackageManifest{
 
 /**
  * @return array
  */
 public function providers(): array
 {
  return $this->config("providers");
 }

 /**
  * @param string $key
  */
 public function config(string $key)
 {
  return isset($this->getManifest()['extra']['oceanwebturk'][$key]) ? $this->getManifest()['extra']['oceanwebturk'][$key] : [];
 }
 
 /**
  * @return array
  */
 public function getManifest(): array
 {
  $return=[];
  $autoPackages=json_decode(file_get_contents(GET_DIRS['VENDOR']."composer/installed.json"),true)['packages'];
  $packages=array_merge([json_decode(file_get_contents(REAL_BASE_DIR."composer.json"),true)],$autoPackages);
  for ($i=0;$i<count($packages);$i++){ 
   $return=array_merge($return,$packages[$i]);
  }
  return $return;
 }
}
