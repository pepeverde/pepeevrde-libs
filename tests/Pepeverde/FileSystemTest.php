<?php

namespace Pepeverde;

use phpDocumentor\Reflection\File;

class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        mkdir(__DIR__ . '/../../build/emptydir', 0777, true);
    }

    public function tearDown()
    {
        parent::tearDown();
        rmdir(__DIR__ . '/../../build/emptydir');
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
