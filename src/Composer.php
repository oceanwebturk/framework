<?php

namespace OceanWebTurk;

class Composer
{   
    use Support\Traits\Macro;

    /**
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postAutoloadDump($event)
    {
        do_action("_composer_post_autoload_dump");
    }

    /**
     * @param \Composer\Script\Event $event
     */
    public static function postInstallCmd($event)
    {
        do_action("_composer_post_install_cmd");
    }

    /**
     * @param \Composer\Script\Event $event
     */
    public static function postUpdateCmd($event)
    {
        do_action("_composer_post_update_cmd");
    }
    
    /**
     * @param string $path
     */
    public static function setPath($path)
    {
        self::$path=$path;
        return new self;
    }
}
