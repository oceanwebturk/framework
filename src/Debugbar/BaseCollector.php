<?php

namespace OceanWebTurk\Debugbar;

use OceanWebTurk\Support\Traits\Macro;

class BaseCollector
{
 use Macro;

 /**
  * @return int|null
  */
 public function getBadgeValue(): int|null
 {
  return null;
 }

 /**
  * @return array
  */
 public function getDataArray(): array
 {
  return array_merge($this->init(),[
   'badgeValue' => $this->getBadgeValue(),
  ]);
 }
}
