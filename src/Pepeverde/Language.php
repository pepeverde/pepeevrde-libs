<?php

namespace Pepeverde;

class Language
{
    /** @var array */
    protected static $languages = [
        'it_IT' => [
            'name' => 'Italiano'
        ],
        'en_US' => [
            'name' => 'Inglese'
        ],
        'de_DE' => [
            'name' => 'Tedesco'
        ],
    ];

    /**
     * @return array
     */
    public function all()
    {
        return self::$languages;
    }

    /**
     * @param $language
     * @return array|false
     */
    public function info($language)
    {
        if (array_key_exists($language, self::$languages)) {
            return self::$languages[$language];
        }

        return false;
    }
}
