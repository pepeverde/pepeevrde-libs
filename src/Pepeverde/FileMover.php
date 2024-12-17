<?php

namespace Pepeverde;

class FileMover
{
    public function move(string $source, string $destination): bool
    {
        return move_uploaded_file($source, $destination);
    }
}
