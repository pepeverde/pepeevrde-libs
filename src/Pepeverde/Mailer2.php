<?php

namespace Pepeverde;

use Html2Text\Html2Text;
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
    private $mailReplyToEmail;

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

        return $this->swiftMailer->send($this->swiftMessage);
    }

    /**
     * @param $template
     * @return $this
     */
    private function initializeTemplate($template)
    {
        try {
            $templatePathParts = pathinfo($template);

            $twig_options = array(
                'cache' => false,
                'auto_reload' => true
            );
            $twig_loader = new \Twig_Loader_Filesystem($templatePathParts['dirname']);
            $twig = new \Twig_Environment($twig_loader, $twig_options);

            $twig->getExtension('Twig_Extension_Core')->setTimezone('Europe/Rome');
            $twig->getExtension('Twig_Extension_Core')->setDateFormat('d/m/Y', '%d days');
            $twig->getExtension('Twig_Extension_Core')->setNumberFormat(2, ',', '');
            $twig_template = $twig->load($templatePathParts['basename']);

            $this->bodyHtml = $twig_template->render($this->templateVars);
            $emogrifier = new Emogrifier($this->bodyHtml);
            $this->bodyHtml = $emogrifier->emogrify();
            $this->bodyText = Html2Text::convert($this->bodyHtml);

            return $this;
        } catch (\Exception $e) {
            Error::report($e);
        }
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

        if (null !== $this->mailReplyToEmail) {
            $this->swiftMessage->addReplyTo($this->mailReplyToEmail);
        }

        return $this;
    }
}
