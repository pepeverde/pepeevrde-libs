<?php

namespace Pepeverde;

use Zigra_Exception;
use Raven_Client;
use Raven_ErrorHandler;

class Error
{
    protected static $ravenClient = null;
    private static $sentryConfig = null;

    public static function getRavenInstance($ravenConfig = null, $appVersion = 'dev')
    {
        if (!isset(self::$ravenClient)) {
            if ($ravenConfig == null) {
                self::$sentryConfig = Registry::get('config.sentry');
            } else {
                self::$sentryConfig = $ravenConfig;
            }

            $sentryServer = self::$sentryConfig['sentry-server'];
            self::$ravenClient = new Raven_Client($sentryServer,
                array(
                    // pass along the version of your application
                    'release' => $appVersion,
                    'extra' => array(
                        'php_version' => phpversion()
                    ),
                ));
        }

        return self::$ravenClient;
    }

    public static function enableErrorHandler($ravenConfig = null, $appVersion = 'dev')
    {
        $raven = self::getRavenInstance($ravenConfig, $appVersion);
        $error_handler = new Raven_ErrorHandler($raven);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();
    }

    public static function report(\Exception $e, $display = true)
    {
        $raven = self::getRavenInstance();
        $event_id = $raven->getIdent($raven->captureException($e));
        if ($raven->getLastError() !== null) {
            printf('There was an error sending the event to Sentry: %s', $raven->getLastError());
        }
        if ($display) {
            Zigra_Exception::renderError(
                $e->getCode(),
                $e->getMessage() . ' [Event-ID: ' . $event_id . ']'
            );
        }
    }

    public static function message(
        $message,
        $params = array(),
        $level_or_options = array(),
        $stack = false,
        $vars = null
    ) {
        $raven = self::getRavenInstance();
        $raven->captureMessage($message, $params, $level_or_options, $stack, $vars);
        if ($raven->getLastError() !== null) {
            printf('There was an error sending the event to Sentry: %s', $raven->getLastError());
        }
    }
}
