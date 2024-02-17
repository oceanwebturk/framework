<?php
namespace OceanWebTurk\Http;

use OceanWebTurk\Autoloader;

trait ManagerDomain
{
 /**
  * @var array
  */
 public static $sites = [];

 public $domainPath;

 /**
  * @param  string $domain
  * @param  array  $configs
  */
 public static function new_site(string $domain,array $configs=[])
 {
  self::$sites[$domain]=$configs;
 }

 /**
  * @param  string $domain
  */
 public function customSite(string $domain)
 {
  $siteConfig=self::$sites[$domain];
  if(isset(GET_DIRS['SITES'])){
  	$this->domainPath=GET_DIRS['SITES'];
  }else{
   $this->domainPath=REAL_BASE_DIR."sites/";
  }
  $this->domainPath.=(isset($siteConfig['folder']) ? $siteConfig['folder'] : $domain).'/';
  $domainConfig=file_exists($this->domainPath.'composer.json') && isset(json_decode(file_get_contents($this->domainPath.'composer.json'),true)['extra']['oceanwebturk']) ? json_decode(file_get_contents($this->domainPath.'composer.json'),true)['extra']['oceanwebturk'] : json_decode(file_get_contents($this->domainPath.'oceanwebturk.json'),true);
  if(isset($domainConfig['domain']['include_file']) && file_exists($this->domainPath.$domainConfig['domain']['include_file'])){
   include($this->domainPath.$domainConfig['domain']['include_file']);
  }
 }

 public function current_site_url($url=null)
 {
  return URL::base().request_uri($this->domainPath).'/'.$url;
 }
}
