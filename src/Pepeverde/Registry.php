<?php

namespace Pepeverde;

class Registry implements \Zigra_RegistryInterface
{
    private static ?self $instance = null;

    /**
     * @var array<string, mixed>
     */
    private static array $vars = [];

    public static function getRegistry(): self
    {
        if (null === self::$instance) {
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
     * @return mixed|null
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::has($key)) {
            return self::$vars[$key];
        }

        return $default;
    }

    public static function set(string $key, $value): bool
    {
        if (!self::has($key)) {
            self::$vars[$key] = $value;

            return true;
        }

        return false;
    }

    /**
     * Add a variable in the $key array.
     *
     * @param string $key   the variable's name
     * @param string $value the variable's value
     */
    public static function add(string $key, string $value): void
    {
        if (self::has($key) && is_array(self::$vars[$key])) {
            self::$vars[$key][] = $value;
        }
    }

    public static function has(?string $key): bool
    {
        if (is_string($key)) {
            return array_key_exists($key, self::$vars);
        }

        // throw new Exception('Key must be a string')
        return false;
    }

    public static function remove(string $key): bool
    {
        if (self::has($key)) {
            unset(self::$vars[$key]);
        }

        return true;
    }

    public static function getAll(): array
    {
        if (!empty(self::$vars)) {
            return self::$vars;
        }

        return [];
    }

    public static function getKeys(): array
    {
        if (!empty(self::$vars)) {
            return array_keys(self::$vars);
        }

        return [];
    }

    public static function clear(): void
    {
        self::$vars = [];
    }

    public function __get(string $key): mixed
    {
        return self::get($key);
    }

    public function __set(string $key, mixed $value)
    {
        self::set($key, $value);
    }

    public function __isset(string $key): bool
    {
        return self::has($key);
    }
}
