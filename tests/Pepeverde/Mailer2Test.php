<?php

namespace Pepeverde\Test;

use Pepeverde\Mailer2;
use PHPUnit\Framework\TestCase;

/**
 * @property Mailer2 $mailer
 */
class Mailer2Test extends TestCase
{
    public function testInstance(): void
    {
        $mailer = new Mailer2(
            __DIR__ . '/../resources/mail-template/mail.html.twig',
            [
                'name' => 'World',
            ],
            [
                'host' => '127.0.0.1',
                'port' => '1025',
                'username' => 'me@example.com',
                'password' => 'SuperSecret',
            ]
        );

        $this->assertInstanceOf(Mailer2::class, $mailer);
    }

    public function testSend(): void
    {
        $mailer = new Mailer2(
            __DIR__ . '/../resources/mail-template/mail.html.twig',
            [
                'name' => 'World',
            ],
            [
                'host' => '127.0.0.1',
                'port' => '1025',
                'username' => 'me@example.com',
                'password' => 'SuperSecret',
            ]
        );
        $mailer->setEmailFromName('Jane Doe');
        $mailer->setEmailFromEmail('janedoe@example.com');
        $mailer->setEmailReplyToEmail('johndoe@example.com');
        $attachment = [
            'name' => 'mail.html.twig',
            'type' => 'text/html',
            'tmp_name' => __DIR__ . '/../resources/mail-template/mail.html.twig',
            'error' => 0,
            'size' => 72,

        ];
        $mailer->setAttachments([$attachment]);
        $count = $mailer->sendMessage(
            'noreplay@example.com',
            'Subject of email'
        );

        $this->assertGreaterThan(0, $count);
    }
}
