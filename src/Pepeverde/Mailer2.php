<?php

namespace Pepeverde;

use Exception;
use Soundasleep\Html2Text;
use Pelago\Emogrifier\CssInliner;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class Mailer2
 */
class Mailer2
{
    public const MAIL_FROM_EMAIL = 'example@example.com';
    public const MAIL_FROM_NAME = 'example@example.com';

    private $subject;

    /** @var Swift_Mailer */
    private $swiftMailer;
    /** @var Swift_Message */
    private $swiftMessage;
    /** @var Swift_SmtpTransport */
    private $swiftTransport;
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

    /**
     * @param string $template
     * @param array $templateVars
     * @param array $swiftOptions
     */
    public function __construct($template, array $templateVars, array $swiftOptions)
    {
        $this->templateVars = $templateVars;
        $this->mailFromEmail = static::MAIL_FROM_EMAIL;
        $this->mailFromName = static::MAIL_FROM_NAME;
        $this->swiftOptions = $swiftOptions;

        $this->initializeTemplate($template);
    }

    /**
     * @param $name
     * @return Mailer2
     */
    public function setEmailFromName($name): self
    {
        $this->mailFromName = $name;

        return $this;
    }

    /**
     * @param $email
     * @return Mailer2
     */
    public function setEmailFromEmail($email): self
    {
        $this->mailFromEmail = $email;

        return $this;
    }

    /**
     * @param $email
     * @return Mailer2
     */
    public function setEmailReplyToEmail($email): self
    {
        $this->mailReplyToEmail = $email;

        return $this;
    }

    /**
     * @param array $attachments
     * @return Mailer2
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function sendMessage(string $to, string $subject, ?string $cc = null, ?string $bcc = null): int
    {
        $this->initializeSwiftMailer($this->swiftOptions);

        $this->subject = $subject;

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

    private function initializeTemplate($template): void
    {
        try {
            $templatePathParts = pathinfo($template);

            $twig_options = [
                'cache' => false,
                'auto_reload' => true
            ];
            $twig_loader = new FilesystemLoader($templatePathParts['dirname']);
            $twig = new Environment($twig_loader, $twig_options);

            $twig->getExtension('Twig_Extension_Core')->setTimezone('Europe/Rome');
            $twig->getExtension('Twig_Extension_Core')->setDateFormat('d/m/Y', '%d days');
            $twig->getExtension('Twig_Extension_Core')->setNumberFormat(2, ',', '');
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
        $this->swiftTransport = Swift_SmtpTransport::newInstance($sm_config['host'], $sm_config['port']);
        $this->swiftMailer = Swift_Mailer::newInstance($this->swiftTransport);
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
