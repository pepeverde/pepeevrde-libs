<?php

namespace Pepeverde\Session;

use Aura\Session\Session;
use RuntimeException;

class SessionFactory
{
    protected $session_config = [
        'lifetime' => 24 * 60 * 60,  // 24 hours
        'httponly' => true,
    ];

    private $sessionName;
    private $savePath;

    /**
     * SessionFactory constructor.
     * @param array  $user_session_config
     * @param string $sessionName
     */
    public function __construct(array $user_session_config = [], $sessionName = 'PHPSESSID')
    {
        $is_https = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $is_https = true;
        }
        $this->session_config['secure'] = $is_https;

        $this->session_config = array_merge($this->session_config, $user_session_config);
        $this->sessionName = $sessionName;
    }

    public function setName($name): void
    {
        $this->sessionName = $name;
    }

    public function setSavePath($savePath): void
    {
        $this->savePath = $savePath;
    }

    public function getSession(string $type): Session
    {
        switch ($type) {
            case 'filesystem':
                return $this->getFilesystemSession();
            case 'redis':
                return $this->getRedisSession();
            default:
                throw new RuntimeException('Session type can be "filesystem" or "redis" only');
        }
    }

    private function getFilesystemSession(): Session
    {
        ini_set('session.save_handler', 'files');

        $session = $this->commonFactory();

        if (!@is_dir($this->savePath)) {
            throw new RuntimeException('Invalid filesystem savepath');
        }
        $session->setSavePath($this->savePath);

        return $session;
    }

    private function getRedisSession(): Session
    {
        ini_set('session.save_handler', 'redis');

        $session = $this->commonFactory();

        $redisPardesUri = parse_url($this->savePath);

        // throw exception if scheme and host are not present
        if (!isset($redisPardesUri['scheme'], $redisPardesUri['host'])) {
            throw new RuntimeException('Invalid Redis savepath');
        }

        $this->savePath = $redisPardesUri['scheme'] . '://' . $redisPardesUri['host'];
        // check if port is specified
        if (
            isset($redisPardesUri['port']) &&
            filter_var($redisPardesUri['port'], FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => 65535,
                    ],
                ]
            )
        ) {
            $this->savePath .= ':' . (int)$redisPardesUri['port'];
        }

        if (isset($redisPardesUri['query'])) {
            $redisPardesUri['query'] = str_replace('auth=&', '', $redisPardesUri['query']);
            $this->savePath .= '?' . $redisPardesUri['query'];
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
