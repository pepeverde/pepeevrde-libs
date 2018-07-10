<?php

namespace Pepeverde;

/**
 * Class Version
 */
class Version
{
    /**
     * @return string
     */
    public static function getVersion()
    {
        return Registry::get('app.version', 'dev');
    }
}
