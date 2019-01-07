<?php

namespace Pepeverde;

use Raven_Client;
use Zigra_Exception;

class Error
{
    /** @var Raven_Client */
    protected static $ravenClient;

    /** @var array */
    private static $sentryConfig;

    /**
     * @param array|null $ravenConfig
     * @param string $appVersion
     * @param string $environment
     * @param array $extra
     * @return Raven_Client
     */
    public static function getRavenInstance($ravenConfig = null, $appVersion = 'dev', $environment = 'development', array $extra = [])
    {
        if (null === self::$ravenClient) {
            self::$sentryConfig = $ravenConfig;
            if ($ravenConfig === null) {
                self::$sentryConfig = Registry::get('config.sentry');
            }

            if (null === $ravenConfig) {
                throw new \RuntimeException('No Sentry configuration available');
            }

            $sentryServer = self::$sentryConfig['sentry-server'];

            $sentryClientConfig = [
                // pass along the version of your application
                'release' => $appVersion,
                // pass along your environment
                'environment' => $environment,
                'tags' => [
                    'php_version' => PHP_VERSION
                ],
                'extra' => $extra,
            ];

            self::$ravenClient = new Raven_Client($sentryServer, $sentryClientConfig);
        }

        return self::$ravenClient;
    }

    /**
     * @param array|null $ravenConfig
     * @param string $appVersion
     * @param string $environment
     * @param array $extra
     * @throws \Raven_Exception
     */
    public static function enableErrorHandler($ravenConfig = null, $appVersion = 'dev', $environment = 'development', array $extra = [])
    {
        $sentryClient = self::getRavenInstance($ravenConfig, $appVersion, $environment, $extra);
        $sentryClient->install();
    }

    /**
     * @param \Exception $e
     * @param bool $display
     * @param array $data
     */
    public static function report(\Exception $e, $display = true, array $data = [])
    {
        $raven = self::getRavenInstance();
        $event_id = $raven->getIdent($raven->captureException($e, $data));
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
