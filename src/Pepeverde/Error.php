<?php

namespace Pepeverde;

use Zigra_Exception;

class Error
{
    private static function configureSentry(
        array $sentryConfig,
        $appVersion,
        $environment,
        array $extra,
        bool $send_default_pii
    ): void
    {
        \Sentry\init([
            'dsn' => $sentryConfig['sentry-server'],
            'release' => $appVersion,
            'environment' => $environment,
            'send_default_pii' => $send_default_pii,
        ]);

        \Sentry\configureScope(static function (\Sentry\State\Scope $scope) use ($extra): void {
            $scope->setExtras($extra);
        });
    }

    /**
     * @param array|null $sentryConfig
     * @param string     $appVersion
     * @param string     $environment
     * @param array      $extra
     * @param bool       $send_default_pii
     */
    public static function enableErrorHandler(
        $sentryConfig = null,
        $appVersion = 'dev',
        $environment = 'development',
        array $extra = [],
        bool $send_default_pii = false
    ): void
    {
        if (null === $sentryConfig) {
            throw new \RuntimeException('No Sentry configuration available');
        }

        self::configureSentry($sentryConfig, $appVersion, $environment, $extra, $send_default_pii);
    }

    /**
     * @param \Exception $e
     * @param bool       $display
     * @param array      $data
     */
    public static function report(\Exception $e, $display = true, array $data = []): void
    {
        if (!empty($data)) {
            \Sentry\configureScope(static function (\Sentry\State\Scope $scope) use ($data): void {
                $scope->setExtras($data);
            });
        }

        $event_id = \Sentry\captureException($e);

        $message = $e->getMessage();
        if (null !== $event_id) {
            $message .= ' [Event-ID: ' . $event_id . ']';
        }
        if (true === $display) {
            Zigra_Exception::renderError(
                $e->getCode(),
                $message
            );
        }
    }

    /**
     * @param string $message
     */
    public static function message($message): void
    {
        \Sentry\captureMessage($message);
    }

    public static function setUser($username, $email = null): void
    {
        \Sentry\configureScope(static function (\Sentry\State\Scope $scope) use ($username, $email): void {
            $scope->setUser([
                'username' => $username,
                'email' => $email
            ]);
        });
    }
}
