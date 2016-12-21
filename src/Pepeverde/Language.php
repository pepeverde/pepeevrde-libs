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
    public static function getLanguagesInfo()
    {
        return self::$languages;
    }

    /**
     * @param $language
     * @return bool|mixed
     */
    public static function getLanguageInfo($language)
    {
        if (array_key_exists($language, self::$languages)) {
            return self::$languages[$language];
        }

        return false;
    }
}
