<?php
namespace OceanWebTurk\Framework\Commands;

class HelpCommand extends BaseCommand
{
  /**
   * @var string
   */
  public $description = "Help";
  
  public function run()
  {
   $this->message(" OceanWebTurk CLI v".OCEANWEBTURK_VERSION,"green");
   $this->br();
   $list='
 ';
   foreach($this->console->commands as $command=>$options){
    $list.=$command.' '.(isset($options['options']['description']) ? $options['options']['description'] : '').'
 ';
   }
   $this->message($list);
  }
}