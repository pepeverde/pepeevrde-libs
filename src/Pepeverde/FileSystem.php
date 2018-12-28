<?php

namespace Pepeverde;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileSystem
{
    /**
     * @param string $dir
     * @return bool
     */
    public static function isDirEmpty($dir)
    {
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);

        return iterator_count($di) === 0;
    }

    /**
     * @param string $name
     * @param int $retinaType
     * @return string
     */
    public static function getRetinaName($name, $retinaType = 2)
    {
        if (!empty($name)) {
            $v = pathinfo($name);

            return $v['filename'] . '@' . $retinaType . 'x.' . $v['extension'];
        }

        return null;
    }

    /**
     * @param string $path
     * @return bool|string
     */
    public static function getFileTypeFromPath($path)
    {
        if (!is_file($path)) {
            return false;
        }

        return self::getFileTypeFromMime(mime_content_type($path));
    }

    /**
     * @param string $mime
     * @return string
     */
    private static function getFileTypeFromMime($mime)
    {
        switch ($mime) {
            case 'application/pdf':
                return 'pdf';
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
            case 'application/vnd.ms-excel':
                return 'excel';
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
            case 'application/msword':
                return 'word';
            case 'application/zip':
            case 'application/x-rar-compressed':
            case 'application/x-7z-compressed':
                return 'archive';
            case 'image/jpeg':
            case 'image/png':
                return 'image';
            default:
                return 'file';
        }
    }

    /**
     * @param int|float|string|null $size
     * @param int $precision
     * @return string
     */
    public static function formatSize($size, $precision = 2)
    {
        if (!is_numeric($size)) {
            return '0';
        }

        if ($size === 0 || $size === '0') {
            return '0';
        }

        $size = (float)$size;

        $base = log($size) / log(1024);
        $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];

        return round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int)floor($base)];
    }

    public static function deleteDir($path)
    {
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }

            rmdir($path);
        } else {
            unlink($path);
        }
    }
}
