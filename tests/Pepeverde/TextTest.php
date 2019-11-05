<?php

namespace Pepeverde\Test;

use Pepeverde\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /** @var Text */
    private $Text;
    private $text_br = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>Mauris volutpat, velit interdum sagittis vestibulum, velit nulla vehicula nulla, nec faucibus est diam sed orci.<br />Phasellus finibus, felis vel posuere dictum, elit arcu vestibulum dolor, non auctor ligula neque eu ante.<br    >';
    private $text_10chars = 'Lorem ipsu';

    private static $provideTestWordWrap;

    public function setUp(): void
    {
        parent::setUp();
        $this->Text = new Text();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Text);
    }

    public function testBr2nl(): void
    {
        $nl = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nMauris volutpat, velit interdum sagittis vestibulum, velit nulla vehicula nulla, nec faucibus est diam sed orci.\nPhasellus finibus, felis vel posuere dictum, elit arcu vestibulum dolor, non auctor ligula neque eu ante.\n";

        $this->assertEquals($nl, $this->Text->br2nl($this->text_br));
    }

    public function testTruncate(): void
    {
        $this->assertEquals('Lorem ipsum dolor sit amet, co...', $this->Text->truncate($this->text_br));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur ad...', $this->Text->truncate($this->text_br, 42));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur ad', $this->Text->truncate($this->text_br, 42, ''));
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetur adipiscing', $this->Text->truncate($this->text_br, 42, '', true));
        $this->assertEquals('Lorem ipsu', $this->Text->truncate($this->text_10chars));
    }

    /*
        public function testTruncateHtml()
        {
            $text4 = '<IMG src="mypic.jpg" /> This image tag is not XHTML conform!<br><hr/><b>But the following image tag should be conform <img src="mypic.jpg" alt="Me, myself and I" /></b><br />Great, or?';
            $text5 = '0<b>1<i>2<span class="myclass">3</span>4<u>5</u>6</i>7</b>8<b>9</b>0';
            $text6 = '<p><strong>Extra dates have been announced for this year\'s tour.</strong></p><p>Tickets for the new shows in</p>';

            $this->assertSame('', $this->Text->truncateHtml($text4));
            $this->assertSame('', $this->Text->truncateHtml($text5));
            $this->assertSame('', $this->Text->truncateHtml($text6));
        }
    */

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider provideTestWordWrap
     * @small
     */
    public function testWordwrap($input, $expected)
    {
        $actual = call_user_func_array(
            array(
                $this->Text,
                'wordwrap'
            ),
            $input
        );
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    public function provideTestWordWrap()
    {
        if (!isset(self::$provideTestWordWrap)) {
            self::$provideTestWordWrap = require __DIR__ . '/../resources/string_tools_wordwrap_data.php';
        }
        return self::$provideTestWordWrap;
    }

    public function testValidStartsWith(): void
    {
        $this->assertTrue(Text::startsWith('start', 's'));
    }

    public function testNotValidStartsWith(): void
    {
        $this->assertFalse(Text::startsWith('start', 't'));
    }

    public function testValidEndsWith(): void
    {
        $this->assertTrue(Text::endsWith('start', 't'));
    }

    public function testNotValidEndsWith(): void
    {
        $this->assertFalse(Text::endsWith('start', 'r'));
    }

    public function testValidContains(): void
    {
        $this->assertTrue(Text::contains('start', 't'));
    }

    public function testNotValidContains(): void
    {
        $this->assertFalse(Text::contains('start', 'k'));
    }
}
