<?php

namespace Pepeverde;

/**
 * Class Version.
 *
 * @deprecated
 */
class Version
{
    public static function getVersion(): string
    {
        return Registry::get('app.version', 'dev');
    }
}
