<?php

namespace OceanWebTurk\Http;

use OceanWebTurk\Import;
use OceanWebTurk\Support\Traits\Macro;

class Controller
{
    use Macro;
    public $import;
    public function __construct()
    {
        $this->import = new Import();
    }
}
