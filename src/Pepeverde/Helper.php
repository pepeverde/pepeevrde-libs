<?php

namespace Pepeverde;

class Helper
{
    /**
     * @param ?string $key
     * @param ?mixed  $default
     *
     * @return array|bool|mixed|string|null
     */
    public static function env($key, $default = null)
    {
        $value = getenv($key);
        if (false === $value) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (mb_strlen($value) > 1 && Text::startsWith($value, '"') && Text::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
