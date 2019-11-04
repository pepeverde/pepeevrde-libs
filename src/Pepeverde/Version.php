<?php

namespace Pepeverde;

/**
 * Class Version
 *
 * @deprecated
 */
class Version
{
    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return Registry::get('app.version', 'dev');
    }
}
