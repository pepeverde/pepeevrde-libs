<?php

namespace Pepeverde;

use Behat\Transliterator\Transliterator;

/**
 * Class Upload.
 */
class Upload
{
    /**
     * @param array       $post_file   $_FILES array for single file
     * @param string      $destination path to put file into
     * @param string|null $name        optional new name of uploaded file
     *
     * @return bool|string
     */
    public static function uploadFile($post_file, $destination, $name = null)
    {
        if (0 === $post_file['error']) {
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
                if (move_uploaded_file($post_file['tmp_name'], $destination . $fullname)) {
                    return $fullname;
                }

                return false;
            } catch (\Exception $e) {
                Error::report($e);
            }

            return false;
        }

        return false;
    }

    /**
     * @param string $name
     */
    public static function cleanName($name): string
    {
        $remove_pattern = '/[^_\-.\-a-zA-Z0-9\s]/u';

        $name = preg_replace($remove_pattern, '', $name); // remove unneeded chars
        $name = str_replace('_', ' ', $name); // treat underscores as spaces
        $name = trim($name); // trim leading/trailing spaces
        $name = preg_replace('/[-\s]+/', '-', $name); // convert spaces to hyphens
        $name = strtolower($name); // convert to lowercase

        return $name;
    }

    /**
     * @param string $filename
     *
     * @return mixed|string
     */
    public static function urlify($filename)
    {
        $sluggableText = Transliterator::transliterate($filename);
        $urlized = strtolower(trim(preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $sluggableText), '-'));
        $urlized = preg_replace("/[\/_|+ -]+/", '-', $urlized);

        return $urlized;
    }

    /**
     * @param array[] $vector
     */
    public static function flipArray($vector): array
    {
        $result = [];
        foreach ($vector as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $result[$key2][$key1] = $value2;
            }
        }

        return $result;
    }

    /**
     * @param string $filepath
     */
    public static function utf8Pathinfo($filepath): array
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
