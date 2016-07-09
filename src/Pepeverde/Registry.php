<?php

namespace Pepeverde;

use Aura\Session\Session;
use Aura\Session\SessionFactory;

class Registry
{
    /** @var Session $session */
    public static $session;

    /**
     * @return \Aura\Session\Segment
     */
    public static function getRegistry()
    {
        $session_factory = new SessionFactory;
        static::$session = $session_factory->newInstance($_COOKIE);

        return static::$session->getSegment('zigra\registry');
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $registry = self::getRegistry();
        $registry->set($key, $value);
    }

    /**
     * @param string $key
     * @param mixed $alt
     * @return mixed
     */
    public static function get($key, $alt = null)
    {
        $registry = self::getRegistry();

        return $registry->get($key, $alt);
    }

    public static function has($key)
    {
        $registry = self::getRegistry();

        return $registry->has($key);
    }
}
