<?php

namespace Pepeverde;

use Zigra_Exception;
use Raven_Client;
use Raven_ErrorHandler;

class Error
{
    /** @var Raven_Client */
    protected static $ravenClient;

    /** @var array */
    private static $sentryConfig;

    /**
     * @param array|null $ravenConfig
     * @param string $appVersion
     * @return Raven_Client
     */
    public static function getRavenInstance($ravenConfig = null, $appVersion = 'dev')
    {
        if (null === self::$ravenClient) {
            self::$sentryConfig = $ravenConfig;
            if ($ravenConfig === null) {
                self::$sentryConfig = Registry::get('config.sentry');
            }

            $sentryServer = self::$sentryConfig['sentry-server'];
            self::$ravenClient = new Raven_Client($sentryServer,
                [
                    // pass along the version of your application
                    'release' => $appVersion,
                    'extra' => [
                        'php_version' => PHP_VERSION
                    ],
                ]);
        }

        return self::$ravenClient;
    }

    /**
     * @param array|null $ravenConfig
     * @param string $appVersion
     */
    public static function enableErrorHandler($ravenConfig = null, $appVersion = 'dev')
    {
        $sentryClient = self::getRavenInstance($ravenConfig, $appVersion);
        $sentryClient->install();
    }

    /**
     * @param \Exception $e
     * @param bool $display
     */
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

    /**
     * @param string $message
     * @param array $params
     * @param array $data
     * @param bool $stack
     * @param null $vars
     */
    public static function message(
        $message,
        array $params = [],
        array $data = [],
        $stack = false,
        $vars = null
    ) {
        $raven = self::getRavenInstance();
        $raven->captureMessage($message, $params, $data, $stack, $vars);
        if ($raven->getLastError() !== null) {
            printf('There was an error sending the event to Sentry: %s', $raven->getLastError());
        }
    }
}
