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

    /**
     * @param string $name
     * @param int $retinaType
     * @return string
     */
    public static function getRetinaName($name, $retinaType = 2)
    {
        if(!empty($name)){
            $v = pathinfo($name);
            $name2x = $v['filename'] . '@'.$retinaType.'x.' . $v['extension'];
            return $name2x;
        }
        return null;
    }
}
