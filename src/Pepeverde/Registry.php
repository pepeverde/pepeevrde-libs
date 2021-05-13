<?php

namespace Pepeverde;

use Zigra_RegistryInterface;

/**
 * Class Registry
 */
class Registry implements Zigra_RegistryInterface
{
    private static $instance;
    private static $vars = [];

    /**
     * @return Registry
     */
    public static function getRegistry(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    /**
     * @param string $key
     * @param mixed $alt
     * @return mixed|null
     */
    public static function get($key, $alt = null)
    {
        if (self::has($key)) {
            return self::$vars[$key];
        }

        return $alt;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        if (!self::has($key)) {
            self::$vars[$key] = $value;

            return true;
        }

        return false;
    }

    /**
     * Add a variable in the $key array
     *
     * @param string $key the variable's name
     * @param string $value the variable's value
     */
    public static function add($key, $value)
    {
        if (self::has($key) && is_array(self::$vars[$key])) {
            self::$vars[$key][] = $value;
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public static function has($key)
    {
        if (is_string($key)) {
            return array_key_exists($key, self::$vars);
        }

        //throw new Exception('Key must be a string')
        return false;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function remove($key): bool
    {
        if (self::has($key)) {
            unset(self::$vars[$key]);
        }

        return true;
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        if (!empty(self::$vars)) {
            return self::$vars;
        }

        return [];
    }

    /**
     * @return array
     */
    public static function getKeys()
    {
        if (!empty(self::$vars)) {
            return array_keys(self::$vars);
        }

        return [];
    }

    public static function clear()
    {
        self::$vars = [];
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        return self::get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        self::set($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return self::has($key);
    }
}
