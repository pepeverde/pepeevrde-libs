<?php

namespace Pepeverde\Test;

use Pepeverde\FileSystem;

class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        if (!is_dir(__DIR__ . '/../../build/emptydir')) {
            mkdir(__DIR__ . '/../../build/emptydir', 0777, true);
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        if (is_dir(__DIR__ . '/../../build/emptydir')) {
            rmdir(__DIR__ . '/../../build/emptydir');
        }
    }

    public function testFullDir()
    {
        $this->assertFalse(FileSystem::isDirEmpty(__DIR__));
    }

    public function testEmptyDir()
    {
        $this->assertTrue(FileSystem::isDirEmpty(__DIR__ . '/../../build/emptydir'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testWrongDir()
    {
        FileSystem::isDirEmpty(__DIR__ . '/iDontExist');
    }

    public function testEmptyName()
    {
        $this->assertEquals('', FileSystem::getRetinaName('', 2));
    }

    public function testRightName()
    {
        $this->assertEquals('nome@2x.jpg', FileSystem::getRetinaName('nome.jpg', 2));
    }

    public function testWrongName()
    {
        $this->assertNotEquals('nome@3x.jpg', FileSystem::getRetinaName('nome.jpg', 2));
    }

    public function testRightEmptyRetinaType()
    {
        $this->assertEquals('nome@2x.jpg', FileSystem::getRetinaName('nome.jpg'));
    }

    public function testWrongEmptyRetinaType()
    {
        $this->assertNotEquals('nome@3x.jpg', FileSystem::getRetinaName('nome.jpg'));
    }

    public function testUTF8Japanese()
    {
        $this->assertEquals('いろはにほへとちりぬるを@3x.png', FileSystem::getRetinaName('いろはにほへとちりぬるを.png', 3));
    }
}
