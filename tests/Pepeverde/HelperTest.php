<?php

namespace Pepeverde\Test;

use Dotenv\Dotenv;
use Pepeverde\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../resources/env/');
        $dotenv->load();
    }

    public function testStringVariable(): void
    {
        $this->assertEquals('string', Helper::env('STRING', 'default'));
    }

    public function testDoubleQuotedStringVariable(): void
    {
        $this->assertEquals('string', Helper::env('DOUBLE_QUOTES_STRING', 'default'));
    }

    public function testNullVariable(): void
    {
        $this->assertNull(Helper::env('NULL', 'default'));
        $this->assertNull(Helper::env('NULL2', 'default'));
    }

    public function testTrueVariable(): void
    {
        $this->assertTrue(Helper::env('TRUE', 'default'));
    }

    public function testFalseVariable(): void
    {
        $this->assertFalse(Helper::env('FALSE', 'default'));
    }

    public function testEmptyStringVariable(): void
    {
        $this->assertEquals('', Helper::env('EMPTY', 'default'));
        $this->assertEquals('', Helper::env('EMPTY2', 'default'));
    }

    public function testIntegerVariable(): void
    {
        $this->assertEquals(5, Helper::env('INTEGER', 'default'));
        $this->assertEquals(5.5, Helper::env('FLOAT', 'default'));
    }

    public function testUTF8JapaneseVariable(): void
    {
        $this->assertEquals('ジャパニーズ', Helper::env('JAPANESE', 'default'));
    }

    public function testNotExitentKeyVariable(): void
    {
        $this->assertEquals('default', Helper::env('Idontexist', 'default'));
        $this->assertNull(Helper::env('Idontexist'));
    }
}
