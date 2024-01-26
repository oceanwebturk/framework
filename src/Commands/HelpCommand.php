<?php

namespace OceanWT\Commands;
use OceanWT\Console;
use OceanWT\Command;
use OceanWT\OceanWT;
class HelpCommand extends Command
{

 /**
  * @param array $params
  */
 public function run(array $params=[])
 {
  $list='
 ';
  ksort(Console::$commands);
  foreach(Console::$commands as$command=>$props){
   $list.=' '.$command.'  '.(isset($props['options']['description']) ? $props['options']['description'] : $props['description']).'
 ';
  }
  echo $list;
 }

 /**
  * @param array  $params
  */
 public function serve(array $params=[])
 {
  $file=file_exists(REAL_BASE_DIR."server.php") ? REAL_BASE_DIR."server.php" : GET_DIRS['SYSTEM'].'Support/CLIServe.php';
  $php = escapeshellarg(PHP_BINARY);
  $host = "localhost";
  $port = intval(isset(OceanWT::$configs['APP_PORT']) ? OceanWT::$configs['APP_PORT'] : 8008);
  $rewrite = escapeshellarg(__DIR__.'/Support/CLIServe.php');
  $command = $php." -S ".$host.":".$port." -t ".GET_DIRS["DIRECTORY_ROOT"];
  passthru($command, $rewrite);
 }
}
