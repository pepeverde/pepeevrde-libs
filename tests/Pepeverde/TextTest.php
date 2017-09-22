<?php

namespace Pepeverde\Test;

use Pepeverde\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /** @var Text */
    private $Text;
    private $text_br = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>Mauris volutpat, velit interdum sagittis vestibulum, velit nulla vehicula nulla, nec faucibus est diam sed orci.<br />Phasellus finibus, felis vel posuere dictum, elit arcu vestibulum dolor, non auctor ligula neque eu ante.<br    >';
    private $text_10chars = 'Lorem ipsu';

    protected function setUp()
    {
        parent::setUp();
        $this->Text = new Text();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Text);
    }

    public function testBr2nl()
    {
        $nl = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nMauris volutpat, velit interdum sagittis vestibulum, velit nulla vehicula nulla, nec faucibus est diam sed orci.\nPhasellus finibus, felis vel posuere dictum, elit arcu vestibulum dolor, non auctor ligula neque eu ante.\n";

        $this->assertEquals($nl, $this->Text->br2nl($this->text_br));
    }

    public function testTruncate()
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
}
