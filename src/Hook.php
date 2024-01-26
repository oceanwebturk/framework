<?php

namespace OceanWT;

class Hook
{
    use Support\Traits\Macro;
    /**
     * @var array
     */
    private static $hooks = array();

    /**
     * @param string   $hook_name
     * @param callable $callback
     */
    public static function add($hook_name, callable $callback)
    {
        if (!isset(self::$hooks[$hook_name])) {
            self::$hooks[$hook_name] = [];
        }

        self::$hooks[$hook_name][] = $callback;
    }
    
    /**
     * @param string $hook_name
     * @param  callable  $callback
     */
    public static function remove($hook_name, callable $callback)
    {
        if (!isset(self::$hooks[$hook_name])) {
            return;
        }

        foreach (self::$hooks[$hook_name] as $key => $hook) {
            if ($hook === $callback) {
                unset(self::$hooks[$hook_name][$key]);
            }
        }
    }

    /**
     * @param string $hook_name
     * @param array  $args
     */
    public static function trigger($hook_name, array $args = array())
    {
        if (!isset(self::$hooks[$hook_name])) {
            return;
        }

        foreach (self::$hooks[$hook_name] as $hook) {
            call_user_func($hook, $args);
        }
    }
}
