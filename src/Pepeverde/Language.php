<?php

namespace Pepeverde;

class Language
{
    /** @var array */
    protected static $languages = [
        'it_IT' => [
            'name' => 'Italiano',
            'natural_name' => 'Italiano'
        ],
        'en_US' => [
            'name' => 'Inglese',
            'natural_name' => 'English'
        ],
        'de_DE' => [
            'name' => 'Tedesco',
            'natural_name' => 'Deutsch'
        ],
        'es_ES' => [
            'name' => 'Spagnolo',
            'natural_name' => 'Español'
        ],
        'fr_FR' => [
            'name' => 'Francese',
            'natural_name' => 'Français'
        ]
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
