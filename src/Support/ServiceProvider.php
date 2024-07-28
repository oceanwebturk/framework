<?php 
namespace OceanWebTurk\Framework\Support;

use OceanWebTurk\Framework\Support\Traits\Macro;

abstract class ServiceProvider
{
    use Macro;   
    /**
     * @var \OceanWebTurk\Framework\Application
     */
    public $app;
    public function __construct($app)
    {
     $this->app = $app;
    }

    /** 
     * @return mixed
     */
    public function boot(){}

    /**
     * @return mixed
     */
    public function register(){}
}