<?php 

use OceanWT\Support\Security\CSRF;

if(!function_exists("csrf_meta")){
 function csrf_meta(){
  return CSRF::meta();
 }
}

if(!function_exists("csrf_input")){
 function csrf_input(){
  return CSRF::input();
 }
}

if(!function_exists("csrf_verify")){
 function csrf_verify(){
  return CSRF::verify();
 }
}
