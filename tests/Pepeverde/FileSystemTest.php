<?php

namespace Pepeverde;

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
}
