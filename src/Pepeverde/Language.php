<?php

namespace Pepeverde;

class Language
{
    /** @var array */
    protected static $languages = array(
        'it_IT' => array(
            'name' => 'Italiano'
        ),
        'en_US' => array(
            'name' => 'Inglese'
        ),
        'de_DE' => array(
            'name' => 'Tedesco'
        ),
    );

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
