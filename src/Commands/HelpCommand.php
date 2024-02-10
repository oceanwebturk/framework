<?php

namespace OceanWebTurk\Commands;
use OceanWebTurk\Console;
use OceanWebTurk\Command;
use OceanWebTurk\OceanWebTurk;
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
  $this->write($list);
 }

 /**
  * @param array  $params
  */
 public function serve(array $params=[])
 {
  $file=file_exists(REAL_BASE_DIR."server.php") ? REAL_BASE_DIR."server.php" : GET_DIRS['SYSTEM'].'Support/CLIServe.php';
  $php = escapeshellarg(PHP_BINARY);
  $host = "localhost";
  $port = intval(isset(OceanWebTurk::$configs['APP_PORT']) ? OceanWebTurk::$configs['APP_PORT'] : 8008);
  $rewrite = escapeshellarg(__DIR__.'/Support/CLIServe.php');
  $command = $php." -S ".$host.":".$port." -t ".GET_DIRS["DIRECTORY_ROOT"];
  passthru($command, $rewrite);
 }
}
