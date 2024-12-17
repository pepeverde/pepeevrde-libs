<?php

namespace Pepeverde\Test;

use Pepeverde\FileSystem;
use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!is_dir(__DIR__ . '/../../build/emptydir')) {
            mkdir(__DIR__ . '/../../build/emptydir', 0777, true);
        }
    }

    protected function tearDown(): void
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
        $this->expectException(\UnexpectedValueException::class);
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

    public function testGetFileTypeFromPathForImage(): void
    {
        $imagePath = __DIR__ . '/../../build/test.jpg';
        copy(__DIR__ . '/../resources/test.jpg', $imagePath);

        $this->assertEquals('image', FileSystem::getFileTypeFromPath($imagePath));

        unlink($imagePath);
    }

    public function testGetFileTypeFromPathForNonExistentFile(): void
    {
        $this->assertFalse(FileSystem::getFileTypeFromPath('/non/existent/file.jpg'));
    }

    public function testFormatSize(): void
    {
        $this->assertEquals('1.00kB', FileSystem::formatSize(1024));
        $this->assertEquals('1.00MB', FileSystem::formatSize(1048576));
        $this->assertEquals('0.00', FileSystem::formatSize('invalid'));
    }

    public function testFormatSizeWithPrecision(): void
    {
        $this->assertEquals('1.50kB', FileSystem::formatSize(1536, 2));
    }

    public function testDeleteFile(): void
    {
        $filePath = __DIR__ . '/../../build/testfile.txt';
        file_put_contents($filePath, 'test content');

        FileSystem::deleteFile($filePath);

        $this->assertFileDoesNotExist($filePath);
    }

    public function testDeleteDir(): void
    {
        $dirPath = __DIR__ . '/../../build/testdir';
        mkdir($dirPath, 0777, true);
        file_put_contents($dirPath . '/file.txt', 'test content');

        FileSystem::deleteDir($dirPath);

        $this->assertFileDoesNotExist($dirPath);
    }

    public function testCheckImageDimensions(): void
    {
        $uploadedDimensions = ['width' => 800, 'height' => 600];
        $requiredDimensions = ['width' => 400, 'height' => 300];

        $this->assertTrue(FileSystem::checkImageDimensions($uploadedDimensions, $requiredDimensions));
    }

    public function testCheckFixedImageDimensions(): void
    {
        $uploadedDimensions = ['width' => 800, 'height' => 600];
        $requiredDimensions = ['width' => 800, 'height' => 600];

        $this->assertTrue(FileSystem::checkFixedImageDimensions($uploadedDimensions, $requiredDimensions));
    }

    public function testCheckIfIsImage(): void
    {
        $allowedTypes = ['image/jpeg', 'image/png'];

        $this->assertTrue(FileSystem::checkIfIsImage('image/jpeg', $allowedTypes));
        $this->assertFalse(FileSystem::checkIfIsImage('application/pdf', $allowedTypes));
    }

    public function testUploadAndVerifyImage(): void
    {
        // Percorso all'immagine reale
        $sourceImage = __DIR__ . '/../resources/test.jpg';
        $this->assertFileExists($sourceImage, 'L\'immagine di test non esiste');

        // Crea un file temporaneo che simula l'upload
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img');
        copy($sourceImage, $tmpFile);

        // Simula l'array $_FILES
        $postFiles = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $tmpFile,
            'error' => \UPLOAD_ERR_OK,
            'size' => filesize($tmpFile),
        ];

        // Configurazione della cartella di destinazione
        $uploadDir = __DIR__ . '/../../build/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $finalFilePath = $uploadDir . '/test.jpg';

        // Mock di FileMover che copia il file realmente
        $fileMoverMock = $this->createMock(\Pepeverde\FileMover::class);
        $fileMoverMock->expects($this->once())
            ->method('move')
            ->with($this->equalTo($tmpFile), $this->equalTo($finalFilePath))
            ->willReturnCallback(function($source, $destination) {
                return copy($source, $destination);
            });

        // Parametri per il test
        $requiredDimensions = ['width' => 250, 'height' => 250];
        $uploadedDimensions = ['width' => true, 'height' => true];
        $allowedTypes = ['image/jpeg'];
        $errors = [];

        // Esegui la funzione
        $result = FileSystem::uploadAndVerifyImage(
            $postFiles,
            $uploadDir,
            $errors,
            $requiredDimensions,
            $uploadedDimensions,
            $allowedTypes,
            $fileMoverMock,
        );

        // Asserzioni
        $this->assertTrue($result['result'], 'Upload failed: ' . implode(', ', $errors));
        $this->assertEquals('test.jpg', $result['image_name']);
        $this->assertFileExists($finalFilePath, 'Il file caricato non esiste nella destinazione');

        // Cleanup
        unlink($finalFilePath);
        unlink($tmpFile);
    }
}
