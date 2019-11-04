<?php

namespace Pepeverde\Test;

use Pepeverde\Registry;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    public function setUp(): void
    {
        Registry::clear();
    }

    public function tearDown(): void
    {
        Registry::clear();
    }

    public function testRegistryGetInstance(): void
    {
        // getting instance initializes instance
        $registry = Registry::getRegistry();
        $this->assertInstanceOf(Registry::class, $registry);
    }

    public function testGet(): void
    {
        Registry::set('foo', 'bar');
        $bar = Registry::get('foo');
        $this->assertEquals('bar', $bar);

        $registryInstance = Registry::getRegistry();
        $this->assertEquals('bar', $registryInstance->foo);
        $this->assertEquals('bar', $registryInstance->get('foo'));

        $this->assertEquals('defaultValue', Registry::get('foo2', 'defaultValue'));
    }

    public function testSet(): void
    {
        // setting value initializes instance
        Registry::set('foo', 'bar');
        $registry = Registry::getRegistry();
        $this->assertInstanceOf(Registry::class, $registry);

        Registry::set('myNullValue', null);
        $nullValue = Registry::get('myNullValue');
        $this->assertNull($nullValue);
    }

    public function testSetAgain(): void
    {
        $this->assertTrue(Registry::set('foo', 'bar'));
        $this->assertFalse(Registry::set('foo', 42));
        $this->assertEquals('bar', Registry::get('foo'));
    }

    public function testAdd(): void
    {
        Registry::set('foo', ['bar']);
        Registry::add('foo', 42);
        Registry::add('foo', 3.14);

        $this->assertEquals(['foo' => ['bar', 42, 3.14]], Registry::getAll());
    }

    public function testHas(): void
    {
        Registry::set('foo', 'bar');
        $this->assertTrue(Registry::has('foo'));
        $this->assertFalse(Registry::has('notfoo'));

        $registry = Registry::getRegistry();

        $this->assertTrue(isset($registry->foo));

        $this->assertFalse(Registry::has(0));
    }

    public function testRemove(): void
    {
        Registry::set('foo', 'bar');
        Registry::set('foo2', 'bar2');
        Registry::remove('foo2');
        $this->assertEquals(['foo' => 'bar'], Registry::getAll());
    }

    public function testGetAll(): void
    {
        $this->assertEquals([], Registry::getAll());

        Registry::set('foo', 'bar');
        $registryInstance = Registry::getRegistry();
        $registryInstance->set('foo2', 42);
        $registryInstance->foo3 = 3.14;

        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], Registry::getAll());
        $this->assertEquals(['foo' => 'bar', 'foo2' => 42, 'foo3' => 3.14], $registryInstance->getAll());
    }

    public function testGetKeys(): void
    {
        $this->assertEquals([], Registry::getKeys());

        Registry::set('foo', 'bar');
        Registry::set('foo2', 42);
        Registry::set('foo3', 3.14);
        $this->assertEquals(['foo', 'foo2', 'foo3'], Registry::getKeys());
    }

    public function testUniqueness(): void
    {
        $firstCall = Registry::getRegistry();
        $secondCall = Registry::getRegistry();

        $this->assertInstanceOf(Registry::class, $firstCall);
        $this->assertSame($firstCall, $secondCall);
    }

    public function testCloning()
    {
        $this->expectException(\Error::class);
        $registry = Registry::getRegistry();
        $cloneRegistry = clone $registry;
    }
}
