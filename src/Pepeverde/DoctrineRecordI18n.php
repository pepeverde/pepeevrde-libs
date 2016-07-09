<?php

namespace Pepeverde;

class DoctrineRecordI18n
{
    /**
     * @param \Doctrine_Record $record
     * @param $name
     * @return string|null
     */
    public static function get(\Doctrine_Record $record, $name)
    {
        $language = Registry::get('language');
        if (isset($record['Translation'][$language]) && '' != $record['Translation'][$language][$name]) {
            return $record['Translation'][$language][$name];
        }
        $defaultLanguage = Registry::get('default_language');

        if (isset($record['Translation'][$defaultLanguage][$name])) {
            return $record['Translation'][$defaultLanguage][$name];
        }

        return null;
    }

    /**
     * @param \Doctrine_Record $record
     * @param $name
     * @return bool
     */
    public static function exists(\Doctrine_Record $record, $name)
    {
        $language = Registry::get('language');
        if (array_key_exists($name, $record['Translation'][$language])) {
            return true;
        }

        $defaultLanguage = Registry::get('default_language');

        return array_key_exists($name, $record['Translation'][$defaultLanguage]->toArray());
    }
}
