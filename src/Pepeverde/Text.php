<?php

namespace Pepeverde;

class Text
{
    /**
     * @param string $string
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public static function substr($string, $start, $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && static::substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if (static::substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function contains($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $string
     * @return null|string|string[]
     */
    public function br2nl($string)
    {
        return preg_replace('#<br\s*?/?>#i', "\n", $string);
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $separator
     * @param bool $preserve
     * @return string
     */
    public function wordwrap($string, $length = 80, $separator = "\n", $preserve = false): string
    {
        $sentences = [];

        $previous = mb_regex_encoding();
        mb_regex_encoding('UTF-8');

        $pieces = mb_split($separator, $string);
        mb_regex_encoding($previous);

        foreach ($pieces as $piece) {
            while (!$preserve && mb_strlen($piece, 'UTF-8') > $length) {
                $sentences[] = static::substr($piece, 0, $length);
                $piece = static::substr($piece, $length, 2048);
            }

            $sentences[] = $piece;
        }

        return implode($separator, $sentences);
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $separator
     * @param bool $preserve
     * @return string
     */
    public function truncate($string, $length = 30, $separator = '...', $preserve = false): string
    {
        if (mb_strlen($string, 'UTF-8') > $length) {
            if (true === $preserve && false !== ($breakpoint = mb_strpos($string, ' ', $length, 'UTF-8'))) {
                $length = $breakpoint;
            }

            return rtrim(static::substr($string, 0, $length)) . $separator;
        }

        return $string;
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $separator
     * @return string
     */
    public function truncateHtml($string, $length = 300, $separator = '&hellip;'): string
    {
        // TODO use mbstring functions
        $i = 0;
        $tags = [];
        $m = [];

        preg_match_all('/<[^>]+>([^<]*)/', $string, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        foreach ($m as $o) {
            if ($o[0][1] - $i >= $length) {
                break;
            }
            $t = static::substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
            if ($t[0] !== '/') {
                $tags[] = $t;
            } elseif (end($tags) === static::substr($t, 1)) {
                array_pop($tags);
            }
            $i += $o[1][1] - $o[0][1];
        }

        return static::substr($string, 0, $length = min(strlen($string), $length + $i)) . (count(
                $tags = array_reverse($tags)
            ) ? '</' . implode(
                    '></',
                    $tags
                ) . '>' : '') . (mb_strlen($string) > $length ? $separator : '');
    }
}
