<?php
namespace OceanWebTurk\Framework\Commands;

class RebuildCommand extends BaseCommand
{
  /**
   * @var string
   */
  public $description = "Rebuild";
  
  public function run()
  {
    $build = config("framework");
    if(isset($build['packages']['composer']['install']) && 
      !empty($build['packages']['composer']['install'])){
      $command = 'composer require '.implode(" ",$build['packages']['composer']['install']);
      echo $this->info($command."\n");
      passthru($command);    
      $this->br();
    }    

    if(isset($build['packages']['composer']['uninstall']) &&
      !empty($build['packages']['composer']['uninstall'])){
      $command = 'composer remove '.implode(" ",$build['packages']['composer']['uninstall']);
      echo $this->info($command."\n");
      passthru($command);
      $this->br();
    }

    if(isset($build['shells']) && !empty($build['shells'])){
      $command = implode(" && ",$build['shells']);
      echo $this->info($command."\n");
      passthru($command);
    }    
  }
}