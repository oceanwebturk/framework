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
  return isset($this->getJsonManifest()['extra']['oceanwebturk'][$key]) ? $this->getJsonManifest()['extra']['oceanwebturk'][$key] : [];
 }
 
 /**
  * @return array
  */
 public function getJsonManifest(): array
 {
  $return=[];
  $packages=json_decode(file_get_contents(GET_DIRS['VENDOR']."composer/installed.json"),true)['packages'];
  for ($i=0;$i<count($packages);$i++){
   $return=array_merge($return,$packages[$i]);
  }
  return $return;
 }

 /**
  * @return array
  */
 public function getXmlManifest(): array
 {

 }
}
