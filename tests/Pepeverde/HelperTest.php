<?php

namespace Pepeverde\Test;

use Dotenv\Dotenv;
use Pepeverde\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $dotenv = new Dotenv(__DIR__ . '/../resources/env/');
        $dotenv->load();
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
        $this->assertNull(Helper::env('NULL', 'default'));
        $this->assertNull(Helper::env('NULL2', 'default'));
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
        $this->assertEquals('', Helper::env('EMPTY2', 'default'));
    }

    public function testIntegerVariable()
    {
        $this->assertEquals(5, Helper::env('INTEGER', 'default'));
        $this->assertEquals(5.5, Helper::env('FLOAT', 'default'));
    }

    public function testUTF8JapaneseVariable()
    {
        $this->assertEquals('いろはにほへとちりぬるを', Helper::env('JAPANESE', 'default'));
    }

    public function testNotExitentKeyVariable()
    {
        $this->assertEquals('default', Helper::env('Idontexist', 'default'));
        $this->assertNull(Helper::env('Idontexist'));
    }
}
