<?php

namespace Pepeverde\Test;

use Pepeverde\FileSystem;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class FileSystemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (!is_dir(__DIR__ . '/../../build/emptydir')) {
            mkdir(__DIR__ . '/../../build/emptydir', 0777, true);
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        if (is_dir(__DIR__ . '/../../build/emptydir')) {
            rmdir(__DIR__ . '/../../build/emptydir');
        }
    }

    public function testFullDir(): void
    {
        $this->assertFalse(FileSystem::isDirEmpty(__DIR__));
    }

    public function testEmptyDir(): void
    {
        $this->assertTrue(FileSystem::isDirEmpty(__DIR__ . '/../../build/emptydir'));
    }

    public function testWrongDir(): void
    {
        $this->expectException(UnexpectedValueException::class);
        FileSystem::isDirEmpty(__DIR__ . '/iDontExist');
    }

    public function testEmptyName(): void
    {
        $this->assertEquals('', FileSystem::getRetinaName('', 2));
    }

    public function testRightName(): void
    {
        $this->assertEquals('nome@2x.jpg', FileSystem::getRetinaName('nome.jpg', 2));
    }

    public function testWrongName(): void
    {
        $this->assertNotEquals('nome@3x.jpg', FileSystem::getRetinaName('nome.jpg', 2));
    }

    public function testRightEmptyRetinaType(): void
    {
        $this->assertEquals('nome@2x.jpg', FileSystem::getRetinaName('nome.jpg'));
    }

    public function testWrongEmptyRetinaType(): void
    {
        $this->assertNotEquals('nome@3x.jpg', FileSystem::getRetinaName('nome.jpg'));
    }

    public function testUTF8Japanese(): void
    {
        $this->assertEquals('いろはにほへとちりぬるを@3x.png', FileSystem::getRetinaName('いろはにほへとちりぬるを.png', 3));
    }
}
