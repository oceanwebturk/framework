<?php 

namespace OceanWT\Support\Security;

use OceanWT\Config;
use OceanWT\Http\Request;

class CSRF
{

  /**
   * @return string
   */
  public static function meta()
  {
   return '<meta name="'.(isset(Config::get("security")->csrf['meta_tag']) ? Config::get("security")->csrf['meta_tag'] : 'csrf-token').'" content="'.self::token().'"/>';
  }

  /**
   * @return string
   */
  public static function input()
  {
   return '<input type="hidden" name="'.(isset(Config::get("security")->csrf['input_name']) ? Config::get("security")->csrf['input_name'] : '__ctoken').'" value="'.self::token().'"/>';
  }

  /**
   * @return string
   */
  public static function token()
  {
   return md5(rand().uniqid(true));
  }

  /**
   * @return bool|boolean
   */
  public static function verify()
  {
   if(Request::isPost()){
    $token=Request::post((isset(Config::get("security")->csrf['input_name']) ? Config::get("security")->csrf['input_name'] : '__ctoken'),true);
    if(isset($token)){
     do_action("_verify_true");
     return true;
    }else{
     do_action("_verify_false");
     return false;
    }
   }
  }

  public function handle()
  {
   $this->verify();
  }
}
