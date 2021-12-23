<?php

namespace Pepeverde;

use finfo;
use SplFileInfo;

class FileInfo extends SplFileInfo
{
    public function getMimeType(): string
    {
        $finfo = new finfo();

        return $finfo->file($this->getRealPath());
    }
}
