<?php

namespace Pepeverde;

use FilesystemIterator;
use RecursiveDirectoryIterator;

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
}
