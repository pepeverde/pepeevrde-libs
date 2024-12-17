<?php

namespace Pepeverde;

class DoctrineRecordI18n
{
    public static function get(\Doctrine_Record $record, string $name): ?string
    {
        $language = Registry::get('language');
        if (isset($record['Translation'][$language]) && '' !== $record['Translation'][$language][$name]) {
            return $record['Translation'][$language][$name];
        }
        $defaultLanguage = Registry::get('default_language');

        return $record['Translation'][$defaultLanguage][$name] ?? null;
    }

    public static function exists(\Doctrine_Record $record, string $name): bool
    {
        $language = Registry::get('language');
        if (array_key_exists($name, $record['Translation'][$language])) {
            return true;
        }

        $defaultLanguage = Registry::get('default_language');

        return array_key_exists($name, $record['Translation'][$defaultLanguage]->toArray());
    }
}
