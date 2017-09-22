<?php

namespace Pepeverde\Test;

use Pepeverde\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testStringVariable()
    {
        $this->assertEquals('string', Helper::env('STRING', 'default'));
    }

    public function testDoubleQuotedStringVariable()
    {
        $this->assertEquals('string', Helper::env('DOUBLE_QUOTES_STRING', 'default'));
    }

    public function testNullVariable()
    {
        $this->assertEquals('default', Helper::env('NULL', 'default'));
    }

    public function testTrueVariable()
    {
        $this->assertEquals(true, Helper::env('TRUE', 'default'));
    }

    public function testFalseVariable()
    {
        $this->assertEquals(false, Helper::env('FALSE', 'default'));
    }

    public function testEmptyStringVariable()
    {
        $this->assertEquals('', Helper::env('EMPTY', 'default'));
    }

    public function testIntegerVariable()
    {
        $this->assertEquals(5, Helper::env('INTEGER', 'default'));
    }

    public function testUTF8JapaneseVariable()
    {
        $this->assertEquals('いろはにほへとちりぬるを', Helper::env('JAPANESE', 'default'));
    }

    public function testValidStartsWith()
    {
        $this->assertTrue(Helper::startsWith('start', 's'));
    }

    public function testNotValidStartsWith()
    {
        $this->assertFalse(Helper::startsWith('start', 't'));
    }

    public function testValidEndsWith()
    {
        $this->assertTrue(Helper::endsWith('start', 't'));
    }

    public function testNotValidEndsWith()
    {
        $this->assertFalse(Helper::endsWith('start', 'r'));
    }

    public function testValidContains()
    {
        $this->assertTrue(Helper::contains('start', 't'));
    }

    public function testNotValidContains()
    {
        $this->assertFalse(Helper::endsWith('start', 'k'));
    }
}
