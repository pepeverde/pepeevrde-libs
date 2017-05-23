<?php

namespace Pepeverde;


class SeoUtilTest extends \PHPUnit_Framework_TestCase
{
    private $text = 'Avendo avuto troppe 💩 visite nel sito, abbiamo provveduto a chiuderlo tutto!';
    private $textWillBeEmpty = 'Avendo 💩 avuto troppe ♻, ® <br> <p class="test">€</p> 
$           ♣ nel ¾ abbiamo tutto!#';

    public function testExtractKeywords()
    {
        $expected = ['visite', 'sito', 'provveduto', 'chiuderlo'];

        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywords($this->text);
        $this->assertSame(count($expected), count($result));
        $this->assertEquals(asort($expected), asort($result));
    }

    public function testExtractKeywordsAsString()
    {
        $expected = 'visite,sito,provveduto,chiuderlo';

        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywordsAsString($this->text);
        $this->assertEquals($expected, $result);
    }

    public function testEmptyResult()
    {
        $expected = [];
        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywords($this->text, 0);
        $this->assertEquals(asort($expected), asort($result));

        $expected = [];
        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywords($this->textWillBeEmpty);
        $this->assertSame(count($expected), count($result));
        $this->assertEquals(asort($expected), asort($result));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMissingStopWordFile()
    {
        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywords($this->text, 10, 'ja');
    }
}
