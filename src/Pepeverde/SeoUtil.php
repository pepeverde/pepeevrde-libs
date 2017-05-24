<?php

namespace Pepeverde;

class SeoUtil
{
    protected $stopWordFilePath = __DIR__ . '/../../resources/stopword/';

    /**
     * @param string $lang
     * @return array
     * @throws \RuntimeException
     */
    private function loadStopWords($lang = 'it')
    {
        $stopWordFile = $this->stopWordFilePath . $lang . '.php';
        if (is_file($stopWordFile)) {
            $stopwords = require $stopWordFile;
            sort($stopwords);

            return $stopwords;
        }

        throw new \RuntimeException('stop word file not found');
    }

    /**
     * @param string $text
     * @param array $stopwords
     * @return array
     */
    private function stripUnwantedCharsFromText($text, $stopwords)
    {
        // strip html tags
        $tmp_string = strip_tags(mb_strtolower($text));

        // remove stopwords
        $pattern = '/\b(?:' . implode('|', $stopwords) . ')\b/iu';
        $tmp_string = preg_replace($pattern, '', $tmp_string);

        // replace (multiple) space like chars with pipe
        $tmp_string = preg_replace('/(\s)+/', '|', $tmp_string);

        // remove emoji
        $tmp_string = $this->stripEmoji($tmp_string);

        // remove any remaining non alphanum chars
        $tmp_string = preg_replace("/[^\p{L}]/u", '|', $tmp_string);

        //replace multiple pipe with single pipe
        $tmp_string = preg_replace('/(\|)+/', '|', $tmp_string);

        //remove pipes at beginning or end of string
        $tmp_string = trim($tmp_string, '|');

        $tmp_array = explode('|', $tmp_string);

        return array_unique(array_filter($tmp_array, 'mb_strlen'));
    }

    /**
     * @param string $string
     * @return string
     */
    private function stripEmoji($string)
    {
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        // Match Flags
        $regexDingbats = '/[\x{1F1E6}-\x{1F1FF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        // Others
        $regexDingbats = '/[\x{1F910}-\x{1F95E}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $regexDingbats = '/[\x{1F980}-\x{1F991}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $regexDingbats = '/[\x{1F9C0}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        $regexDingbats = '/[\x{1F9F9}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }

    /**
     * @param string $text
     * @param int $limit
     * @param string $lang
     * @return array
     * @throws \RuntimeException
     */
    public function extractKeywords($text, $limit = 10, $lang = 'it')
    {
        if (is_int($limit) && $limit > 0) {
            $stopWords = $this->loadStopWords($lang);
            $cleanTextAsArray = $this->stripUnwantedCharsFromText($text, $stopWords);

            return array_slice($cleanTextAsArray, 0, $limit);
        }

        return [];
    }

    /**
     * @param string $text
     * @param int $limit
     * @param string $lang
     * @return string
     * @throws \RuntimeException
     */
    public function extractKeywordsAsString($text, $limit = 10, $lang = 'it')
    {
        return implode(',', $this->extractKeywords($text, $limit, $lang));
    }
}
