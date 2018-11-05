<?php

namespace Pepeverde\Session;

class SessionFactory
{
    protected $session_config = [
        'lifetime' => 24 * 60 * 60,  // 24 hours
        'httponly' => true,
    ];

    private $sessionName = 'PHPSESSID';
    private $savePath;

    /**
     * SessionFactory constructor.
     * @param array $user_session_config
     */
    public function __construct(array $user_session_config = [])
    {
        $is_https = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $is_https = true;
        }
        $this->session_config['secure'] = $is_https;

        $this->session_config = array_merge($this->session_config, $user_session_config);
    }

    public function setName($name)
    {
        $this->sessionName = $name;
    }

    public function setSavePath($savePath)
    {
        $this->savePath = $savePath;
    }

    /**
     * SessionFactory constructor.
     * @param string $type
     * @throws \RuntimeException
     * @return \Aura\Session\Session
     */
    public function getSession($type)
    {
        switch ($type) {
            case 'filesystem':
                return $this->getFilesystemSession();
                break;
            case 'redis':
                return $this->getRedisSession();
                break;
            default:
                throw new \RuntimeException('Session type can be "filesystem" or "redis" only');
        }
    }

    /**
     * @throws \RuntimeException
     * @return \Aura\Session\Session
     */
    private function getFilesystemSession()
    {
        ini_set('Session.save_handler', 'files');

        $session = $this->commonFactory();

        if (!@is_dir($this->savePath)) {
            throw new \RuntimeException('Invalid filesystem savepath');
        }
        $session->setSavePath($this->savePath);

        return $session;
    }

    /**
     * @throws \RuntimeException
     * @return \Aura\Session\Session
     */
    private function getRedisSession()
    {
        ini_set('Session.save_handler', 'redis');

        $session = $this->commonFactory();

        $redisPardesUri = parse_url($this->savePath);

        // throw exception if scheme and host are not present
        if (!isset($redisPardesUri['scheme'], $redisPardesUri['host'])) {
            throw new \RuntimeException('Invalid Redis savepath');
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

    /**
     * @return \Aura\Session\Session
     */
    private function commonFactory()
    {
        ini_set('Session.gc_maxlifetime', $this->session_config['lifetime']);
        $session_factory = new \Aura\Session\SessionFactory();
        $session = $session_factory->newInstance($_COOKIE);
        $session->setCookieParams($this->session_config);
        $session->setName($this->sessionName);

        return $session;
    }
}
