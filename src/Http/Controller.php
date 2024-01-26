<?php

namespace OceanWT\Http;

use OceanWT\Import;
use OceanWT\Support\Traits\Macro;

class Controller
{
    use Macro;
    public $import;
    public function __construct()
    {
        $this->import = new Import();
    }
}
