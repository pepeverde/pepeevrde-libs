<?php

namespace Pepeverde;

use finfo;
use SplFileInfo;

class FileInfo extends SplFileInfo
{
    /**
     * @return string
     */
    public function getMimeType(): string
    {
        $finfo = new finfo();

        return $finfo->file($this->getRealPath());
    }
}
