<?php

namespace OceanWebTurk\Database;

use OceanWebTurk\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
 public function boot()
 {
  $this->cli->command("db:create",[DatabaseCommand::class,'create'],[
   "description"=>"Generate a Database"
  ]);

  $this->cli->command("db:drop",[DatabaseCommand::class,'drop'],[
   "description"=>"Delete Database"
  ]);
  $this->cli->command("migrate",[DatabaseCommand::class,'migrate'],[
   "description"=>"Migration creator"
  ]);
  $this->cli->command("make:migration",[DatabaseCommand::class,'migration'],[
   "description"=>"Make a Migration"
  ]);
  $this->cli->command("migrate:rollback",[DatabaseCommand::class,'migrate_rollback'],[
   "description"=>"Migration rollback"
  ]);
 }
}
