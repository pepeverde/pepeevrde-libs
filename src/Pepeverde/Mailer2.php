<?php

namespace Pepeverde;

use Exception;
use Pelago\Emogrifier\CssInliner;
use Soundasleep\Html2Text;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Class Mailer2.
 */
class Mailer2
{
    public const MAIL_FROM_EMAIL = 'example@example.com';
    public const MAIL_FROM_NAME = 'example@example.com';

    /** @var Swift_Mailer */
    private $swiftMailer;
    /** @var Swift_Message */
    private $swiftMessage;
    /** @var array */
    private $swiftOptions;

    private $mailFromEmail;
    private $mailFromName;
    private $mailReplyToEmail;

    private $templateVars;
    private $bodyHtml;
    private $bodyText;
    /** @var array */
    private $attachments = [];

    public function __construct(string $template, array $templateVars, array $swiftOptions)
    {
        $this->templateVars = $templateVars;
        $this->mailFromEmail = static::MAIL_FROM_EMAIL;
        $this->mailFromName = static::MAIL_FROM_NAME;
        $this->swiftOptions = $swiftOptions;

        $this->initializeTemplate($template);
    }

    public function setEmailFromName(string $name): self
    {
        $this->mailFromName = $name;

        return $this;
    }

    public function setEmailFromEmail(string $email): self
    {
        $this->mailFromEmail = $email;

        return $this;
    }

    public function setEmailReplyToEmail(string $email): self
    {
        $this->mailReplyToEmail = $email;

        return $this;
    }

    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function sendMessage(string $to, string $subject, ?string $cc = null, ?string $bcc = null): int
    {
        $this->initializeSwiftMailer($this->swiftOptions);

        $this->swiftMessage->setTo($to);
        if (null !== $cc) {
            $this->swiftMessage->addCc($cc);
        }
        if (null !== $bcc) {
            $this->swiftMessage->setBcc($bcc);
        }

        $this->swiftMessage->setSubject($subject);

        $this->swiftMessage->setBody($this->bodyHtml, 'text/html');
        $this->swiftMessage->addPart($this->bodyText, 'text/plain');
        $this->manageAttachments();

        return $this->swiftMailer->send($this->swiftMessage);
    }

    private function initializeTemplate(string $template): void
    {
        try {
            $templatePathParts = pathinfo($template);

            $twig_options = [
                'cache' => false,
                'auto_reload' => true,
            ];
            $twig_loader = new FilesystemLoader($templatePathParts['dirname']);
            $twig = new Environment($twig_loader, $twig_options);

            /** @var CoreExtension $Twig_Extension_Core */
            $Twig_Extension_Core = $twig->getExtension('Twig_Extension_Core');
            $Twig_Extension_Core->setTimezone('Europe/Rome');
            $Twig_Extension_Core->setDateFormat('d/m/Y', '%d days');
            $Twig_Extension_Core->setNumberFormat(2, ',', '');
            $twig_template = $twig->load($templatePathParts['basename']);

            $this->bodyHtml = $twig_template->render($this->templateVars);
            $this->bodyHtml = CssInliner::fromHtml($this->bodyHtml)->inlineCss()->render();
            $this->bodyText = Html2Text::convert($this->bodyHtml);
        } catch (Exception $e) {
            Error::report($e);
        }
    }

    private function initializeSwiftMailer(array $sm_config): void
    {
        $swiftTransport = Swift_SmtpTransport::newInstance($sm_config['host'], $sm_config['port']);
        if (array_key_exists('username', $sm_config)) {
            $swiftTransport->setUsername($sm_config['username']);
        }
        if (array_key_exists('password', $sm_config)) {
            $swiftTransport->setPassword($sm_config['password']);
        }
        if (array_key_exists('authmode', $sm_config)) {
            $swiftTransport->setAuthMode($sm_config['authmode']);
        }
        if (array_key_exists('encryption', $sm_config)) {
            $swiftTransport->setEncryption($sm_config['encryption']);
        }
        $this->swiftMailer = Swift_Mailer::newInstance($swiftTransport);
        $this->swiftMessage = Swift_Message::newInstance()
            ->setFrom([$this->mailFromEmail => $this->mailFromName]);

        if (null !== $this->mailReplyToEmail) {
            $this->swiftMessage->addReplyTo($this->mailReplyToEmail);
        }
    }

    private function manageAttachments(): void
    {
        try {
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $id => $file_path) {
                    if (!is_file($file_path)) {
                        unset($this->attachments[$id]);
                    } else {
                        $this->swiftMessage->attach(Swift_Attachment::fromPath($file_path));
                    }
                }
            }
        } catch (Exception $e) {
            Error::report($e);
        }
    }
}
