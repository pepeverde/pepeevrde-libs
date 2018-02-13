<?php

namespace Pepeverde\Test;


use Pepeverde\FileInfo;

class FileInfoTest extends \PHPUnit_Framework_TestCase
{
    private $fileinfo;

    protected function setUp()
    {
        parent::setUp();
        $this->fileinfo = new FileInfo(__FILE__);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->fileinfo);
    }

    public function testMimeType()
    {
        $this->assertSame($this->fileinfo->getMimeType(), 'PHP script, ASCII text');
    }
}
