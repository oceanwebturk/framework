<?php
namespace OceanWebTurk\Framework\Commands;

class ServeCommand extends BaseCommand
{
 public function run()
 {
  $file=file_exists(REAL_BASE_DIR."server.php") ? REAL_BASE_DIR."server.php" : GET_DIRS['SYSTEM'].'Support/CLIServe.php';
  $php = escapeshellarg(PHP_BINARY);
  $host = "127.0.0.1";
  $port = 7868;
  $rewrite = escapeshellarg(__DIR__.'/Support/CLIServe.php');
  $command = $php." -S ".$host.":".$port." -t  public/";
  passthru($command, $rewrite);
 }
}