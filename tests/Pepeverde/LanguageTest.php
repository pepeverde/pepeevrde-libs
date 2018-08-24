<?php

namespace Pepeverde\Test;

use Pepeverde\Language;

class LanguageTest extends \PHPUnit_Framework_TestCase
{
    /** @var Language */
    private $Language;

    protected function setUp()
    {
        parent::setUp();
        $this->Language = new Language();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Language);
    }

    /**
     * @dataProvider languageProvider
     * @param mixed $languageCode
     * @param mixed $expectedName
     */
    public function testInfoName($languageCode, $expectedName)
    {
        $this->assertEquals($expectedName, $this->Language->info($languageCode)['name']);
    }

    /**
     * @dataProvider naturalLanguageProvider
     * @param mixed $languageCode
     * @param mixed $expectedName
     */
    public function testInfoNaturalName($languageCode, $expectedName)
    {
        $this->assertEquals($expectedName, $this->Language->info($languageCode)['natural_name']);
    }

    public function testInfoNotExists()
    {
        $this->assertFalse($this->Language->info('tlh_001'));
    }

    public function testAll()
    {
        $languages = [
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

        $this->assertEquals($languages, $this->Language->all());
    }

    public function languageProvider()
    {
        return [
            ['it_IT', 'Italiano'],
            ['en_US', 'Inglese'],
            ['de_DE', 'Tedesco'],
            ['es_ES', 'Spagnolo'],
            ['fr_FR', 'Francese'],
        ];
    }

    public function naturalLanguageProvider()
    {
        return [
            ['it_IT', 'Italiano'],
            ['en_US', 'English'],
            ['de_DE', 'Deutsch'],
            ['es_ES', 'Español'],
            ['fr_FR', 'Français'],
        ];
    }
}
