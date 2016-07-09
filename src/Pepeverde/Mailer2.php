<?php

namespace Pepeverde;

use HtmlToText\HtmlToText;
use Pelago\Emogrifier;

/**
 * Class Mailer2
 *
 * @version 20151103
 */
class Mailer2
{
    const MAIL_FROM_EMAIL = 'example@example.com';
    const MAIL_FROM_NAME = 'example@example.com';

    private $subject;
    private $template;

    /** @var \Swift_Mailer */
    private $swiftMailer;
    /** @var \Swift_Message */
    private $swiftMessage;
    /** @var \Swift_SmtpTransport */
    private $swiftTransport;
    /** @var array */
    private $swiftOptions;

    private $mailFromEmail;
    private $mailFromName;
    private $mailReplyToEmail = null;

    private $templateVars;
    private $bodyHtml;
    private $bodyText;

    /**
     * @param string $template
     * @param array $templateVars
     * @param array $swiftOptions
     */
    public function __construct($template, array $templateVars, array $swiftOptions)
    {
        $this->template = $template;
        $this->templateVars = $templateVars;
        $this->mailFromEmail = static::MAIL_FROM_EMAIL;
        $this->mailFromName = static::MAIL_FROM_NAME;
        $this->swiftOptions = $swiftOptions;

        self::initializeTemplate($this->template);
    }

    /**
     * @param $name
     * @return Mailer2
     */
    public function setEmailFromName($name)
    {
        $this->mailFromName = $name;

        return $this;
    }

    /**
     * @param $email
     * @return Mailer2
     */
    public function setEmailFromEmail($email)
    {
        $this->mailFromEmail = $email;

        return $this;
    }

    /**
     * @param $email
     * @return Mailer2
     */
    public function setEmailReplyToEmail($email)
    {
        $this->mailReplyToEmail = $email;

        return $this;
    }

    /**
     * @param string $to
     * @param string $subject
     * @param null $cc
     * @param null $bcc
     * @return mixed
     */
    public function sendMessage($to, $subject, $cc = null, $bcc = null)
    {
        self::initializeSwiftMailer($this->swiftOptions);

        $this->subject = $subject;

        $this->swiftMessage->setTo($to);
        if (!is_null($cc)) {
            $this->swiftMessage->addCc($cc);
        }
        if (!is_null($bcc)) {
            $this->swiftMessage->setBcc($bcc);
        }

        $this->swiftMessage->setSubject($subject);

        $this->swiftMessage->setBody($this->bodyHtml, 'text/html');
        $this->swiftMessage->addPart($this->bodyText, 'text/plain');

        return $this->swiftMailer->send($this->swiftMessage);
    }

    /**
     * @param $template
     * @return Mailer2
     */
    private function initializeTemplate($template)
    {
        $templatePathParts = pathinfo($template);

        $twig_options = array(
            'cache' => false,
            'auto_reload' => true
        );
        $twig_loader = new \Twig_Loader_Filesystem($templatePathParts['dirname']);
        $twig = new \Twig_Environment($twig_loader, $twig_options);

        $twig->getExtension('core')->setTimezone('Europe/Rome');
        $twig->getExtension('core')->setDateFormat('d/m/Y', '%d days');
        $twig->getExtension('core')->setNumberFormat(2, ',', '');
        $twig_template = $twig->loadTemplate($templatePathParts['basename']);

        $this->bodyHtml = $twig_template->render($this->templateVars);
        $emogrifier = new Emogrifier($this->bodyHtml);
        $this->bodyHtml = $emogrifier->emogrify();
        $converter = new HtmlToText($this->bodyHtml);
        $this->bodyText = $converter->convert();

        return $this;
    }

    /**
     * @param array $sm_config
     * @return Mailer2
     */
    private function initializeSwiftMailer(array $sm_config)
    {
        $this->swiftTransport = \Swift_SmtpTransport::newInstance($sm_config['host'], $sm_config['port']);
        $this->swiftMailer = \Swift_Mailer::newInstance($this->swiftTransport);
        $this->swiftMessage = \Swift_Message::newInstance()
            ->setFrom(array($this->mailFromEmail => $this->mailFromName));

        if (!is_null($this->mailReplyToEmail)) {
            $this->swiftMessage->addReplyTo($this->mailReplyToEmail);
        }

        return $this;
    }
}