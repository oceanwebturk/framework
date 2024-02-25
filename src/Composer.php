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
        Hook::trigger("_composer_post_autoload_dump");
    }

    /**
     * @param \Composer\Script\Event $event
     */
    public static function post_install_cmd($event)
    {
        Hook::trigger("_composer_post_install_cmd");
    }

    /**
     * @param \Composer\Script\Event $event
     */
    public static function post_update_cmd($event)
    {
        Hook::trigger("_composer_post_update_cmd");
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
