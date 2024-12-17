<?php

namespace Pepeverde;

class Error
{
    /**
     * @param array<string, mixed> $sentryConfig
     * @param array<string, mixed> $extra
     */
    private static function configureSentry(
        array $sentryConfig,
        string $appVersion,
        string $environment,
        array $extra,
        bool $send_default_pii,
    ): void {
        \Sentry\init([
            'dsn' => $sentryConfig['sentry-server'],
            'release' => $appVersion,
            'environment' => $environment,
            'send_default_pii' => $send_default_pii,
        ]);

        \Sentry\configureScope(static function(\Sentry\State\Scope $scope) use ($extra): void {
            $scope->setExtras($extra);
        });
    }

    /**
     * @param array<string, mixed>|null $sentryConfig
     * @param array<string, mixed>      $extra
     */
    public static function enableErrorHandler(
        ?array $sentryConfig = null,
        string $appVersion = 'dev',
        string $environment = 'development',
        array $extra = [],
        bool $send_default_pii = false,
    ): void {
        if (null === $sentryConfig || 0 === count($sentryConfig)) {
            throw new \RuntimeException('No Sentry configuration available');
        }

        self::configureSentry($sentryConfig, $appVersion, $environment, $extra, $send_default_pii);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function report(\Exception $e, bool $display = true, array $data = []): void
    {
        if (!empty($data)) {
            \Sentry\configureScope(static function(\Sentry\State\Scope $scope) use ($data): void {
                $scope->setExtras($data);
            });
        }

        $eventId = \Sentry\captureException($e);

        $message = $e->getMessage();
        if (null !== $eventId) {
            $message .= ' [Event-ID: ' . $eventId . ']';
        }
        if (true === $display) {
            \Zigra_Exception::renderError(
                $e->getCode(),
                $message
            );
        }
    }

    public static function message(string $message): void
    {
        \Sentry\captureMessage($message);
    }

    public static function setUser(string $username, ?string $email = null): void
    {
        \Sentry\configureScope(static function(\Sentry\State\Scope $scope) use ($username, $email): void {
            $scope->setUser(
                \Sentry\UserDataBag::createFromArray(
                    [
                        'username' => $username,
                        'email' => $email,
                    ]
                )
            );
        });
    }
}
