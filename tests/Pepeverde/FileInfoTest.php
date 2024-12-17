<?php

namespace Pepeverde\Test;

use Pepeverde\FileInfo;
use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase
{
    private $fileinfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileinfo = new FileInfo(__FILE__);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->fileinfo);
    }

    public function testMimeType(): void
    {
        $this->assertSame($this->fileinfo->getMimeType(), 'PHP script, ASCII text');
    }
}
