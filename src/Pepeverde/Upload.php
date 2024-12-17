<?php

namespace Pepeverde;

use Behat\Transliterator\Transliterator;

class Upload
{
    /**
     * @param array       $post_file   $_FILES array for single file
     * @param string      $destination path to put file into
     * @param string|null $name        optional new name of uploaded file
     */
    public static function uploadFile(
        array $post_file,
        string $destination,
        ?string $name = null,
        ?FileMover $fileMover = null,
    ): bool|string {
        $fileMover = $fileMover ?? new FileMover();

        if (\UPLOAD_ERR_OK === $post_file['error']) {
            try {
                if (!@mkdir($destination, 0777, true) && !is_dir($destination)) {
                    throw new \RuntimeException('impossibile creare directory: ' . dirname($destination));
                }
            } catch (\RuntimeException $e) {
                Error::report($e);
            }

            if (null === $name) {
                $name = $post_file['name'];
            }
            $name_fileinfo = self::utf8Pathinfo($name);

            $name = self::cleanName($name_fileinfo['filename']);
            $fullname = $name . '.' . $name_fileinfo['extension'];
            $destination = rtrim($destination, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

            try {
                if ($fileMover->move($post_file['tmp_name'], $destination . $fullname)) {
                    return $fullname;
                }
            } catch (\Exception $e) {
                Error::report($e);
            }
        }

        return false;
    }

    public static function cleanName(string $name): string
    {
        $remove_pattern = '/[^a-zA-Z0-9\s.\-_]/u';

        // Rimuove caratteri indesiderati ma lascia spazi, punti, trattini e underscore
        $name = preg_replace($remove_pattern, '', $name);

        // Tratta gli underscore come spazi e rimuove spazi iniziali/finali
        $name = str_replace('_', ' ', $name);
        $name = trim($name);

        // Converte spazi multipli in trattini
        $name = preg_replace('/\s+/', '-', $name);

        // Rimuove trattini subito prima del punto
        $name = preg_replace('/-\./', '.', $name);

        // Riduce puntini consecutivi a un singolo punto
        $name = preg_replace('/\.{2,}/', '.', $name);

        // Converte tutto in minuscolo
        $name = strtolower($name);

        return $name;
    }

    public static function urlify(string $filename): string
    {
        $pathInfo = self::utf8Pathinfo($filename); // Usa utf8Pathinfo per ottenere le parti del file

        $name = Transliterator::transliterate($pathInfo['filename']);
        $name = strtolower(trim(preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $name), '-'));
        $name = preg_replace("/[\/_|+ -]+/", '-', $name);

        return isset($pathInfo['extension']) ? $name . '.' . $pathInfo['extension'] : $name;
    }

    /**
     * @param array[] $vector
     */
    public static function flipArray(array $vector): array
    {
        $result = [];
        foreach ($vector as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $result[$key2][$key1] = $value2;
            }
        }

        return $result;
    }

    public static function utf8Pathinfo(string $filepath): array
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);
        $ret = [];
        if ($m[1]) {
            $ret['dirname'] = $m[1];
        }
        if ($m[2]) {
            $ret['basename'] = $m[2];
        }
        if ($m[5]) {
            $ret['extension'] = $m[5];
        }
        if ($m[3]) {
            $ret['filename'] = $m[3];
        }

        return $ret;
    }
}
