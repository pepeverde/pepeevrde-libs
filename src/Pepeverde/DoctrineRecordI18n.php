<?php

namespace Pepeverde;

use Doctrine_Record;

class DoctrineRecordI18n
{
    /**
     * @param string $name
     */
    public static function get(Doctrine_Record $record, $name): ?string
    {
        $language = Registry::get('language');
        if (isset($record['Translation'][$language]) && '' !== $record['Translation'][$language][$name]) {
            return $record['Translation'][$language][$name];
        }
        $defaultLanguage = Registry::get('default_language');

        return $record['Translation'][$defaultLanguage][$name] ?? null;
    }

    /**
     * @param string $name
     */
    public static function exists(Doctrine_Record $record, $name): bool
    {
        $language = Registry::get('language');
        if (array_key_exists($name, $record['Translation'][$language])) {
            return true;
        }

        $defaultLanguage = Registry::get('default_language');

        return array_key_exists($name, $record['Translation'][$defaultLanguage]->toArray());
    }
}
