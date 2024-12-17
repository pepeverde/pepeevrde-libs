<?php

namespace Pepeverde\Session;

use Aura\Session\Session;

class SessionFactory
{
    protected array $session_config = [
        'lifetime' => 24 * 60 * 60,  // 24 hours
        'httponly' => true,
    ];

    private string $sessionName;
    private string $savePath;

    public function __construct(
        array $user_session_config = [],
        string $sessionName = 'PHPSESSID',
    ) {
        $is_https = false;
        if (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS']) {
            $is_https = true;
        }
        $this->session_config['secure'] = $is_https;

        $this->session_config = array_merge($this->session_config, $user_session_config);
        $this->sessionName = $sessionName;
    }

    public function setName(string $name): void
    {
        $this->sessionName = $name;
    }

    public function setSavePath(string $savePath): void
    {
        $this->savePath = $savePath;
    }

    public function getSession(string $type): Session
    {
        return match ($type) {
            'filesystem' => $this->getFilesystemSession(),
            'redis' => $this->getRedisSession(),
            default => throw new \RuntimeException('Session type can be "filesystem" or "redis" only'),
        };
    }

    private function getFilesystemSession(): Session
    {
        ini_set('session.save_handler', 'files');

        $session = $this->commonFactory();

        if (!@is_dir($this->savePath)) {
            throw new \RuntimeException('Invalid filesystem savepath');
        }
        $session->setSavePath($this->savePath);

        return $session;
    }

    private function getRedisSession(): Session
    {
        ini_set('session.save_handler', 'redis');

        $session = $this->commonFactory();

        $redisParsedUri = parse_url($this->savePath);

        // throw exception if scheme and host are not present
        if (!isset($redisParsedUri['scheme'], $redisParsedUri['host'])) {
            throw new \RuntimeException('Invalid Redis savepath');
        }

        $this->savePath = $redisParsedUri['scheme'] . '://' . $redisParsedUri['host'];
        // check if port is specified
        if (
            isset($redisParsedUri['port'])
            && filter_var(
                $redisParsedUri['port'],
                \FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => 65535,
                    ],
                ]
            )
        ) {
            $this->savePath .= ':' . (int)$redisParsedUri['port'];
        }

        if (isset($redisParsedUri['query'])) {
            $redisParsedUri['query'] = str_replace('auth=&', '', $redisParsedUri['query']);
            $this->savePath .= '?' . $redisParsedUri['query'];
        }

        $session->setSavePath($this->savePath);

        return $session;
    }

    private function commonFactory(): Session
    {
        ini_set('session.gc_maxlifetime', $this->session_config['lifetime']);

        $session_factory = new \Aura\Session\SessionFactory();
        $session = $session_factory->newInstance($_COOKIE);
        $session->setCookieParams($this->session_config);
        $session->setName($this->sessionName);

        return $session;
    }
}
