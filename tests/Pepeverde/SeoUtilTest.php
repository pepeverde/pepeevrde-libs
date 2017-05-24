<?php

namespace Pepeverde;


class SeoUtilTest extends \PHPUnit_Framework_TestCase
{
    private $text = 'Avendo avuto troppe 💩 visite nel sito, abbiamo provveduto à chiuderlo tutto!';
    private $textWillBeEmpty = 'Avendo 💩 avuto troppe ♻, ® <br> <p class="test">€</p> 
$           ♣ nel ¾ abbiamo tutto!#';
    private $problematicText = array(
        0 => 'PermasteelisaCampus è un programma completamente gratuito indirizzato a coloro che mirano a diventare dei professionisti nel settore industriale e dei servizi di ingegneria e che saranno progressivamente in grado di gestire - da un punto di vista tecnico e gestionale - progetti complessi, dando il proprio supporto in tutte le fasi di studio, realizzazione, implementazione e produzione di commesse di medie e grandi dimensioni in ambito nazionale ed internazionale.',
        1 => 'I servizi fotografici per la moda e advertising richiedono un accurato processo di pianificazione. Ci occupiamo dell’intero progetto, dalla ricerca delle location al casting per i modelli, fino al servizio fotografico e la post produzione degli scatti selezionati.',
    );
    private $expectedFromProblematicText = array(
        0 => array(
            'PermasteelisaCampus',
            'programma',
            'gratuito',
            'indirizzato',
            'coloro',
            'mirano',
            'diventare',
            'professionisti',
            'settore',
            'industriale',
            'servizi',
            'ingegneria',
            'grado',
            'gestire',
            'punto',
            'vista',
            'tecnico',
            'gestionale',
            'progetti',
            'complessi',
            'supporto',
            'fasi',
            'studio',
            'realizzazione',
            'implementazione',
            'produzione',
            'commesse',
            'medie',
            'grandi',
            'dimensioni',
            'ambito',
            'nazionale',
            'internazionale'
        ),
        1 => array(
            'servizi',
            'fotografici',
            'moda',
            'advertising',
            'accurato',
            'processo',
            'pianificazione',
            'occupiamo',
            'intero',
            'progetto',
            'ricerca',
            'location',
            'casting',
            'modelli',
            'servizio',
            'fotografico',
            'post',
            'produzione',
            'scatti',
            'selezionati'
        ),
    );

    public function testExtractKeywords()
    {
        $expected = ['visite', 'sito', 'provveduto', 'chiuderlo'];

        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywords($this->text);
        $this->assertSame(count($expected), count($result));
        $this->assertEquals($expected, $result);
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
        $this->assertEquals($expected, $result);

        $expected = [];
        $seoUtil = new SeoUtil();
        $result = $seoUtil->extractKeywords($this->textWillBeEmpty);
        $this->assertSame(count($expected), count($result));
        $this->assertEquals($expected, $result);
    }

    public function testSpecificString()
    {
        $seoUtil = new SeoUtil();
        foreach ($this->problematicText as $key => $string) {
            $expected = mb_strtolower(implode(',', $this->expectedFromProblematicText[$key]));
            $result = $seoUtil->extractKeywordsAsString($string, 100);
            $this->assertEquals($expected, $result);
        }
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
