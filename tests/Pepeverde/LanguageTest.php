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
    public function testInfo($languageCode, $expectedName)
    {
        $this->assertEquals($expectedName, $this->Language->info($languageCode)['name']);
    }

    public function testInfoNotExists()
    {
        $this->assertFalse($this->Language->info('tlh_001'));
    }

    public function testAll()
    {
        $languages = [
            'it_IT' => [
                'name' => 'Italiano'
            ],
            'en_US' => [
                'name' => 'Inglese'
            ],
            'de_DE' => [
                'name' => 'Tedesco'
            ],
            'es_ES' => [
                'name' => 'Spagnolo'
            ],
            'fr_FR' => [
                'name' => 'Francese'
            ],
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
}
