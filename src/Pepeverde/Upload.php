<?php
namespace Pepeverde;

use Behat\Transliterator\Transliterator;
use Zigra_Exception;

class Upload
{
    public static function uploadFile($post_file, $destination, $name = null)
    {
        if ($post_file['error'] == 0) {
            try {
                if (!is_dir(dirname($destination))) {
                    if (!mkdir(dirname($destination), 0777, true)) {
                        throw new \Exception('impossibile creare directory: ' . dirname($destination));
                    }
                }
            } catch (\Exception $e) {
                Error::report($e);
            }
            if (null == $name) {
                $name = $post_file['name'];
            }
            $name_fileinfo = self::utf8Pathinfo($name);

            $remove_pattern = '/[^_\-.\-a-zA-Z0-9\s]/u';
            $name = preg_replace($remove_pattern, '', $name_fileinfo['filename']); // remove unneeded chars
            $name = str_replace('_', ' ', $name); // treat underscores as spaces
            $name = preg_replace('/^\s+|\s+$/', '', $name); // trim leading/trailing spaces
            $name = preg_replace('/[-\s]+/', '-', $name); // convert spaces to hyphens
            $name = strtolower($name); // convert to lowercase
            $fullname = $name . '.' . $name_fileinfo['extension'];
            try {
                if (move_uploaded_file($post_file['tmp_name'], $destination . $fullname)) {
                    return $fullname;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                Error::report($e);
            }

            return false;
        }

        return false;
    }

    public static function urlify($filename)
    {
        $sluggableText = Transliterator::transliterate($filename, '-');
        $urlized = strtolower(trim(preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $sluggableText), '-'));
        $urlized = preg_replace("/[\/_|+ -]+/", '-', $urlized);

        return $urlized;
    }

    public static function flipArray($vector)
    {
        $result = array();
        foreach ($vector as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $result[$key2][$key1] = $value2;
            }
        }

        return $result;
    }

    public static function utf8Pathinfo($filepath)
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);
        $ret = array();
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
