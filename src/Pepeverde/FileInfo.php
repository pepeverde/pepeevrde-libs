<?php

namespace Pepeverde;

class FileInfo extends \SplFileInfo
{
    /**
     * @return string
     */
    public function getMimeType()
    {
        $finfo = new \finfo();

        return $finfo->file($this->getRealPath());
    }
}
