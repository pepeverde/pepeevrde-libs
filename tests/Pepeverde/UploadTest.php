<?php

namespace Pepeverde\Test;

use Pepeverde\FileMover;
use Pepeverde\Upload;
use PHPUnit\Framework\TestCase;

class UploadTest extends TestCase
{
    private string $testFile;
    private string $uploadDir;

    protected function setUp(): void
    {
        $this->uploadDir = __DIR__ . '/../../build/uploads';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $this->testFile = tempnam(sys_get_temp_dir(), 'TST');
        file_put_contents($this->testFile, 'Test file content');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }

        foreach (glob($this->uploadDir . '/*') as $file) {
            unlink($file);
        }

        rmdir($this->uploadDir);
    }

    public function testUploadFileSuccess(): void
    {
        $mockMover = $this->createMock(FileMover::class);
        $mockMover->method('move')
            ->willReturnCallback(function($source, $destination) {
                // Simula il movimento copiando il file nella destinazione
                return copy($source, $destination);
            });

        $postFile = [
            'name' => 'test_file.txt',
            'type' => 'text/plain',
            'tmp_name' => $this->testFile,
            'error' => 0,
            'size' => filesize($this->testFile),
        ];

        $result = Upload::uploadFile($postFile, $this->uploadDir, null, $mockMover);

        $this->assertEquals('test-file.txt', $result);
        $this->assertFileExists($this->uploadDir . '/' . $result);
    }

    public function testUploadFileWithCustomName(): void
    {
        $mockMover = $this->createMock(FileMover::class);
        $mockMover->method('move')
            ->willReturnCallback(function($source, $destination) {
                // Simula il movimento copiando il file nella destinazione
                return copy($source, $destination);
            });

        $postFile = [
            'name' => 'original_name.txt',
            'type' => 'text/plain',
            'tmp_name' => $this->testFile,
            'error' => 0,
            'size' => filesize($this->testFile),
        ];

        $customName = 'custom-name.txt';
        $result = Upload::uploadFile($postFile, $this->uploadDir, $customName, $mockMover);

        $this->assertEquals($customName, $result);
        $this->assertFileExists($this->uploadDir . '/' . $customName);
    }

    public function testUploadFileError(): void
    {
        $postFile = [
            'name' => 'test_file.txt',
            'tmp_name' => '',
            'error' => 4, // UPLOAD_ERR_NO_FILE
            'size' => 0,
        ];

        $result = Upload::uploadFile($postFile, $this->uploadDir);
        $this->assertFalse($result);
    }

    public function testCleanName(): void
    {
        $this->assertEquals('test-file.txt', Upload::cleanName('Test_File@! #.txt'));
        $this->assertEquals('another-test-file.txt', Upload::cleanName('another test file .txt'));
        $this->assertEquals('file-name.jpg', Upload::cleanName('file name....jpg'));
        $this->assertEquals('simple-test.txt', Upload::cleanName('simple test!@#.txt'));
    }

    public function testUrlify(): void
    {
        $filename = 'Ciao Mondo! äöü.jpg';
        $result = Upload::urlify($filename);
        $this->assertEquals('ciao-mondo-aou.jpg', $result);
    }

    public function testUtf8Pathinfo(): void
    {
        $filepath = '/path/to/file/test_file.txt';
        $result = Upload::utf8Pathinfo($filepath);

        $this->assertEquals([
            'dirname' => '/path/to/file',
            'basename' => 'test_file.txt',
            'extension' => 'txt',
            'filename' => 'test_file',
        ], $result);
    }

    public function testFlipArray(): void
    {
        $input = [
            'col1' => ['row1' => 1, 'row2' => 2],
            'col2' => ['row1' => 3, 'row2' => 4],
        ];

        $expected = [
            'row1' => ['col1' => 1, 'col2' => 3],
            'row2' => ['col1' => 2, 'col2' => 4],
        ];

        $this->assertEquals($expected, Upload::flipArray($input));
    }
}
