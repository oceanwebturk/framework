<?php 
namespace OceanWebTurk\Framework\Support;

class Security
{
 public function csrfInput()
 {
  return '<input type="hidden" name="'.config('system:form_security')['csrf_input_name'].'" value="'.md5(uniqid()).'">';
 }
}